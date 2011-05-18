<?php
/*
 * Copyright (c) 2011 Kamil Kupiec, kamil.kupiec[at]gmail.com
 *
 * Licensed under the MIT license:
 *     http://www.opensource.org/licenses/mit-license.php
 *
*/

require_once 'Database.php';
require_once 'Configuration.php';

// Some constants

define('NL', "\n");
define('TB', '    ');

// ------

/**
 * Row mapper class
 *
 * Maps only tables with primary keys.
 * Identification of a row without a PK is not possible here.
 */
class FancyMapper {
    /**
     * Database singleton
     * @var Database
     */
    private $db;
    /**
     * Configuration singleton
     * @var Configuration
     */
    private $conf;
    private $handler;
    private $table;
    private $pk;
    private $pkSkip = false;

    // select column as alias
    private $columns;
    private $aliases;

    private $tableClass;
    private $editableClass;

    /**
     * @param String $table name of the Table
     * @param String $pk name of the Primary Key
     */
    public function __construct($table, $pk) {
        $this->table = $table;
        $this->pk = $pk;
        $this->db = Database::getInstance();
        $this->conf = Configuration::getInstance();

        $this->columns = array();
        $this->aliases = array();
        array_push($this->columns, $pk);
        array_push($this->aliases, 'ID');

        $this->tableClass = 'fancy-table';
        $this->editableClass = 'fancy-editable';
        $this->handler = 'fancyhandler.php';
    }

    /**
     *
     * @param String $column_name
     * @param String $display_as
     * @return FancyMapper return self for chaining ;)
     */
    public function addParam($column_name, $display_as=false) {
        array_push($this->columns, $column_name);
        if(!$display_as)
            array_push($this->aliases, $column_name);
        else
            array_push($this->aliases, $display_as);
        return $this;
    }

    /**
     *
     * @param String $class CSS class name for the &lt;table&gt; tag, deafult: fancy-table
     * @return FancyMapper return self for chaining ;)
     */
    public function setTableClass($class) {
        $this->tableClass = $class;
        return $this;
    }

    /**
     *
     * @param String $editableClass class name for editable elements, default: fancy-editable
     * @return FancyMapper return self for chaining ;)
     */
    public function setEditableClass($editableClass) {
        $this->editableClass = $editableClass;
        return $this;
    }

    /**
     * Generates kickass HTML code
     * @return String
     */
    public final function generateTable() {
        // verification
        if(count($this->columns) == 0)
            return 'No fields added to Fancy Mapper (table: ' . $this->table . ')';

        $query = 'SELECT ';
        $out = NL;

        // build the query
        // @TODO maybe modify to some ADO select function
        $i = 0;
        $colCount = count($this->columns);

        foreach ($this->columns as $col) {
            $query .= $col;
            $i++;
            if($i != $colCount)
                $query .= ', ';
            else
                $query .= ' ';
        }

        $query .= 'FROM ' . $this->table;

        $result = $this->db->Execute($query);

        if(!$result)
            $out = $this->db->conn->ErrorMsg();
        else {
            $out .= '<table class="' . $this->tableClass . '">' . NL;

            // table head generation - aliases
            $out .= TB . '<thead>' . NL;
            $out .= TB.TB . '<tr>' . NL;

            foreach ($this->aliases as $alias) {
                if(!$this->pkSkip || strcmp($alias, 'ID') != 0) {
                    $out .= TB.TB.TB;
                    if(strcmp($alias, 'ID') == 0)
                        $out .= '<td class="fancy-id-class">';
                    else
                        $out .= '<td>';
                    $out .= $alias . '</td>' . NL;
                }
            }

            // deletion header
            $out .= TB.TB.TB . '<td>Delete</td>' . NL;

            $out .= TB.TB . '</tr>' . NL;
            $out .= TB . '</thead>' . NL;

            // table body generation - SELECT results
            $out .= TB . '<tbody>' . NL;

            while(!$result->EOF) {
                $rowId = $result->fields[$this->columns[0]];

                $out .= TB.TB . '<tr>' . NL;
                for ($i = $this->pkSkip ? 1 : 0; $i < $colCount; $i++) {
                    $out .= TB.TB.TB . '<td>';
                    if($i == 0)
                        $out .= $rowId;
                    else
                        $out .= $this->decorateField($result->fields[$this->columns[$i]], $this->columns[$i], $rowId);
                    $out .= '</td>' . NL;
                }

                // deletion links
                $out .= TB.TB.TB . '<td>' . $this->decorateDeleteLink($rowId) . '</td>' . NL;
                $out .= TB.TB . '</tr>' . NL;

                $result->MoveNext();
            }
            $out .= TB . '</tbody>' . NL;
            $out .= '</table>' . NL;
            $out .= NL;

            $out .= '<input type="button" value="' . $this->conf->getLabel('form_add') . '" id="fancy-add-button"/>' .NL;
            // @TODO generate the form
            $out .= '<div id="fancy-add-form">' .NL;
            $out .= TB . '<form method="post" action="' . Configuration::getHttpHost() . '/ajax/' . $this->handler . '">' .NL;
            for($i = 1; $i < $colCount; $i++) {
                $out .= TB.TB . $this->aliases[$i] . ': <input name="' . $this->columns[$i] . '" type="text" width="100"/><br/>' .NL;
            }
            // column names
            $out .= TB.TB . '<input name="colnames" type="hidden" value="';
            for($i = 1; $i < $colCount; $i++) {
                $out .= $this->columns[$i];
                if($i != ($colCount - 1))
                    $out .= ':';
            }
            $out .= '"/>' .NL;
            $out .= TB.TB . '<input name="tablename" type="hidden" value="' . $this->table . '"/>'.NL;
            $out .= TB.TB . '<input name="action" type="hidden" value="add"/>'.NL;
            $out .= TB.TB . '<input type="submit" value="' . $this->conf->getLabel('form_submit') . '" id="fancy-add-submit"/>' .NL;
            $out .= TB . '</form>' .NL;
            $out .= '</div>' .NL;
        }

        return $out;

    }

    /**
     * Decorates the field with html.
     * Adds <b>class</b> param and <b>id</b> param
     * @param String $value
     * @param String $column
     * @param String $rowId
     * @return String
     */
    private function decorateField($value, $column, $rowId) {
        $result = '<span class="' . $this->editableClass . '" id="' . $rowId . ':' . $column . '">';
        $result .= $value;
        $result .='</span>';

        return $result;
    }

    private function decorateDeleteLink($rowId) {
        $result = '<span class="fancy-delete" id="del:' . $rowId . '">x</span>';
        return $result;
    }

    /**
     * Generates kickass jQuery script
     * @return String
     */
    public function generateScript() {

        $script = NL;
        $script .= '$(function() {' .NL;

        // some pre-show doings
        $script .= TB . '$(\'#fancy-add-form\').hide();' .NL;
        // ----

        $script .= TB . '$(\'.' . $this->editableClass . '\').editable(\'' . Configuration::getHttpHost() . '/ajax/' . $this->handler . '\', {' .NL;
        $script .= TB.TB . 'submit  : \'' . $this->conf->getLabel('edit_save') . '\',' .NL;
        $script .= TB.TB . 'cancel  : \'' . $this->conf->getLabel('edit_cancel') . '\',' .NL;
        $script .= TB.TB . 'tooltip : \'' . $this->conf->getLabel('edit_tooltip') . '\',' .NL;
        $script .= TB.TB . 'submitdata: { action: \'edit\', table : \'' . $this->table . '\', pk : \'' . $this->pk . '\'}' .NL;
        $script .= TB . '});' .NL;

        // delete action
        $script .= TB . '$(\'.' . 'fancy-delete' . '\').click(function() {' .NL;
        $script .= TB.TB . 'if (confirm(\'' . $this->conf->getLabel('delete_question') . '\')) {' .NL;
        $script .= TB.TB.TB . 'var id = this.id;' .NL;
        $script .= TB.TB.TB . 'var data = \'action=del&table=' . $this->table . '&pk=' . $this->pk . '&id=\' + this.id;' .NL;
        $script .= TB.TB.TB . '$.post(\'' . Configuration::getHttpHost() . '/ajax/' . $this->handler . '\', data, function(response) {' .NL;
        $script .= TB.TB.TB.TB . '$(\'#postresponse\').html(response);' .NL;
        $script .= TB.TB.TB . '});' .NL;
        $script .= TB.TB.TB . '$(this).parent().parent().fadeOut();' .NL;
        $script .= TB.TB . '}' .NL;
        $script .= TB . '});' .NL;

        // add action
        $script .= TB . '$(\'#' . 'fancy-add-button' . '\').click(function() {' .NL;
        $script .= TB.TB . '$(\'#fancy-add-form\').slideDown();' .NL;
        $script .= TB . '});' .NL;

        $script .= '});' .NL;
        $script .= NL;
        return $script;
    }

}

?>
