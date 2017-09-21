<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php
include_once 'functions.php';
include_once 'Netcarver/Textile/Parser.php';
require './vendor/autoload.php';



if(isset($_POST['html_input']) && !empty($_POST['html_input'])){
    //$html_input = filter_var($_POST['html_input'], FILTER_SANITIZE_STRING);
    $html_input = $_POST['html_input'];
    $textile_output = htmlToTextile($html_input);
    
    echo "<div style='display: none;'>";
    echo "<pre>";
    var_dump($textile_output);
    echo "<br>";
    var_dump($html_input);
    echo "</pre>";
    echo "</div>";
}
?>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <title>From Html to Textile</title>
    </head>
    <body>
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6">
                    <form action="<?php $_SERVER['SCRIPT_NAME']?>" method="post">
                        <button class="btn btn-primary" type="submit">Konwertuj!</button>
                        <div class="form-group">
                            <label for="html_input">Html:</label>
                            <textarea name="html_input" id="html_input" rows="8" class="form-control"><?php if (isset($html_input) && !empty($html_input)){ echo $html_input; }?></textarea>
                        </div>
                        <?php
                        if(isset($textile_output)){
                            echo "<div class='form-group'><label for='textile_output'>Textile:</label><textarea id='textile_output' rows='8' class='form-control'>".$textile_output."</textarea></div>";
                        }
            ?>
                    </form>
                </div>
                <div class="col-lg-6">
                 <?php
                    if(isset($textile_output)){
                        $parser = new Parser();
                        echo $parser->parse($textile_output);
                    }
                    ?>
                </div>
            </div>
        </div>                                                    
    </body>
</html>
