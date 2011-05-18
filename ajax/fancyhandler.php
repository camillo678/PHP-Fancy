<?php
/*
 * Copyright (c) 2011 Kamil Kupiec, kamil.kupiec[at]gmail.com
 *
 * Licensed under the MIT license:
 *     http://www.opensource.org/licenses/mit-license.php
 *
*/

/**
 * PHP script for handling AJAX calls:
 *
 // * Create a record:
 // * action: add,
 // * table,
 // * pk,
 // * value
 *
 * Delete a record:
 * action: del,
 * table,
 * pk,
 * id - formed 'del:<id>'
 */

$response = '';

if(isset($_POST['action'])) {
    $in['action'] = $_POST['action'];

    if($in['action'] === 'del') {

        if(isset($_POST['id']) && isset($_POST['table']) && isset($_POST['pk'])) {

            $in['id'] = $_POST['id'];
            $in['table'] = $_POST['table'];
            $in['pk'] = $_POST['pk'];

            $id_array = explode(':', $in['id']);

            if(isset($id_array) && count($id_array) == 2) {
                $out['id'] = $id_array[1];

                // @TODO maybe some paranoid validation? ;)
                $out['table'] = $in['table'];
                $out['pk'] = $in['pk'];

                // @TODO move this to a DAO class
                require_once dirname(__FILE__) . '/../classes/Database.php';

                $db = Database::getInstance();
                $query = 'DELETE FROM ' . $out['table'] . ' WHERE ' . $out['pk'] . '=?';
                $result = $db->Execute($query, array($out['id']));

                if(!$result)
                    $response = 'Error d44';
                else
                    $response =  'deleted: ' . $out['id'];
            }
            else {
                $response = 'Error d02';
            }
        }
        else
            $response = 'Error d01';
    }
    else if($in['action'] === 'edit') {
        if(isset($_POST['id']) && isset($_POST['value']) && isset($_POST['table']) && isset($_POST['pk'])) {

            $in['id'] = $_POST['id'];
            $in['value'] = $_POST['value'];
            $in['table'] = $_POST['table'];
            $in['pk'] = $_POST['pk'];

            $id_array = explode(':', $in['id']);

            if(isset($id_array) && count($id_array) == 2) {
                $out['id'] = $id_array[0];
                $out['column'] = $id_array[1];

                // @TODO maybe some paranoid validation? ;)
                $out['value'] = $in['value'];
                $out['table'] = $in['table'];
                $out['pk'] = $in['pk'];

                // @TODO move this to a DAO class
                require_once dirname(__FILE__) . '/../classes/Database.php';

                $db = Database::getInstance();
                $query = 'UPDATE ' . $out['table'] . ' SET ' . $out['column'] . '=? WHERE ' . $out['pk'] . '=?';
                $result = $db->Execute($query, array($out['value'], $out['id']));

                if(!$result)
                    $response = 'Error e44';
                else
                    $response =  $out['value'];
            }
            else {
                $response = 'Error e02';
            }
        }
        else
            $response = 'Error e01';
    }
    else if($in['action'] === 'add') {

        if(isset($_POST['colnames']) && isset($_POST['tablename'])) {
            $in['colnames'] = $_POST['colnames'];
            $in['tablename'] = $_POST['tablename'];
            $col_array = explode(':', $in['colnames']);

            $count = count($col_array);

            if($count != 0) {
                $out['tablename'] = $in['tablename'];

                $out['col_names'] = '';
                $out['col_values'] = array();
                $out['col_?'] = '';
                $i = 1;
                foreach ($col_array as $name) {
                    $out['col_names'] .= $name;
                    array_push($out['col_values'], $_POST[$name]);
                    $out['col_?'] .= '?';
                    if($i != $count) {
                        $out['col_names'] .= ',';
                        $out['col_?'] .= ',';
                    }
                    $i++;
                }

                // @TODO move this to a DAO class
                require_once dirname(__FILE__) . '/../classes/Database.php';

                $db = Database::getInstance();
                $query = 'INSERT INTO ' . $out['tablename'] . '(' . $out['col_names'] . ') VALUES (' . $out['col_?'] . ')';
                $result = $db->Execute($query, $out['col_values']);

                if(!$result)
                    $response = 'Error a44';
                else
                    $response =  'added';
            }
            else
                $response = 'Error a02';

        }
        else {
            $response = 'Error a01';
        }
    }
}
else
    $response = 'Error 00';

echo $response;

?>
