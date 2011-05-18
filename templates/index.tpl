<!doctype html>
<html>
    <head>
        <meta charset="utf-8"/>
        <!--<link rel="stylesheet" href="{$base_url}/css/reset.css"/>-->
        <link rel="stylesheet" href="{$base_url}/css/default.css"/>
        <title>FancyPHP</title>

        <script src="{$base_url}/js/jquery-1.4.2.min.js"></script>
        <script src="{$base_url}/js/jquery.jeditable.mini.js"></script>
        <script>{$script}
        </script>
    </head>
    <body>
        <p>Table: Persons</p>

        <div class="table">

            {$table}

        </div>

        <div class="postresponse"><span id="postresponse"></span></div>

    </body>
</html>