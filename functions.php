<?php
function htmlToTextile($html){
    
    
//    $html = htmlspecialchars($html); // interpetacja tagow jakos znaki asci
    
    $html = tagToTextile($html);
    $html = listaToTextile($html);
    $html = preg_replace("/<br.*>/"," ",$html); //zamienia br na \n
    //$html = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n",$html); //usuwam puste wiersze
    //$html = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n",$html); //usuwam puste wiersze
    $html = preg_replace("/#br/", "\n \n", $html);
    
    $textile = $html;
    //return str_replace(array("#\\","/#"),array(">","<"),strip_tags($textile, '<pre>'));
    return $textile;
}

function tagToTextile($html) {
        $html = preg_split("/(<.*>)/U",$html,-1,PREG_SPLIT_DELIM_CAPTURE);
        foreach($html as $line){
            $line = trim($line);
            if (preg_match('/<p>/',$line)){
                $line = preg_replace('/<p>/','', $line);
            }elseif (preg_match('/<*.p>/',$line)){
                $line = "#br";
            }elseif (preg_match('/<i>/',$line)){
               $line = preg_replace('/<i>/',' __', $line);
            }elseif (preg_match('/<\/i>/',$line)){
                $line = preg_replace('/<\/i>/','__ ', $line);
            }elseif (preg_match('/<em>/',$line)){
               $line = preg_replace('/<em>/',' _', $line);
            }elseif (preg_match('/<\/em>/',$line)){
                $line = preg_replace('/<\/em>/','_ ', $line);
            }elseif (preg_match('/<strong>/',$line)){
               $line = preg_replace('/<strong>/',' *', $line);
            }elseif (preg_match('/<\/strong>/',$line)){
                $line = preg_replace('/<\/strong>/','* ', $line);
            }elseif (preg_match('/<b>/',$line)){
               $line = preg_replace('/<b>/',' **', $line);
            }elseif (preg_match('/<\/b>/',$line)){
                $line = preg_replace('/<\/b>/','** ', $line);
            }
            $glyph_out[] = $line;
        }
        return $html = implode('',$glyph_out);
    }


function listaToTextile($html) {
    $list = "";    
    $licz= 0;
    $licz_u = 0;
//        $w = 0;
//        echo "<pre>";
//        var_dump($html);
//        echo "</pre>";
        $html = preg_split("/(<.*>)/U",$html,-1,PREG_SPLIT_DELIM_CAPTURE);
        //$html = preg_split("/(&lt;.*&gt;)/U",$html,-1,PREG_SPLIT_DELIM_CAPTURE);
//        echo "<pre>";
//        var_dump($html);
//        echo "</pre>";
        foreach($html as $line){
//            echo $line; echo $w++."<br>";
            if (preg_match('/<ol>/',$line) ){
                $line="";
                $list = "o";
                $licz++;
            }elseif (preg_match('/<ul>/',$line) ){
                $line="";
                $list = "u";
                $licz_u++;
            }elseif (preg_match('/<\/ol>/',$line)){
                $line = "\n";
                $licz--;
            }elseif (preg_match('/<\/ul>/',$line)){
                $line = "\n";
                $licz_u--;
            }elseif (preg_match('/<\/li>/',$line)){
                $line = "\n";
            }elseif ($list == 'o'){
                $line = trim($line);
                $show = "";
                for($i=0;$i < $licz;$i++){
                    $show .= "#";
                }
                $show .= " ";
                $line = preg_replace('/<li>/',$show, $line);
            }elseif ($list == 'u'){
                $line = trim($line);
                $show = "";
                for($i=0;$i < $licz_u;$i++){
                    $show .= "*";
                }
                $show .= " ";
                $line = preg_replace('/<li>/',$show, $line);
                
            }
            $glyph_out[] = $line;
        }
        return $html = implode('',$glyph_out);
    }