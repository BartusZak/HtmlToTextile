<?php
//działa możlowośc edycji OOP
class html2textile {
    function detextile($text) {
       $text = preg_replace("/<br[ \/]*>\s*/","\n",$text); //zamienia br na \n
       //$text = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n",$text); //usuwam puste wiersze
       
        //wyieramy ktore tagi chcemy convert na Textile
        $oktags = array('ol','ul','li','i','b','em','strong');
        
//        //usuwam tagi img
//        $text = preg_replace_callback("/\t*<(img)\s*([^>]*)>/Usi",array($this,'processTag'),$text);
        
        foreach($oktags as $tag){
           $text = preg_replace_callback("/\t*<(".$tag.")\s*([^>]*)>(.*)<\/\\1>/Usi",array($this,'processTag'),$text);
        }
       
//        //zamiena glyphow
//        $text = $this->detextile_process_glyphs($text);

       //zamieniam listę 
       $text = $this->detextile_process_lists($text);
       
//        $text = preg_replace('/^\t* *p\. /m','',$text);
    
    //zwracam wyniki usuwajac inne tagi
    return str_replace(array("#\\","/#"),array(">","<"),strip_tags($this->decode_high($text), '<pre>'));
       
//strip_tags($text);
    }
    function detextile_process_glyphs($text) {
        $glyphs = array(
            '’'=>'\'',        # single closing
            '‘'=>'\'',        # single opening
            '”'=>'"',         # double closing
            '“'=>'"',         # double opening
            '—'=>'--',        # em dash
            '–'=>' - ',       # en dash
            '×' =>'x',         # dimension sign
            '™'=>'(TM)',      # trademark
            '®' =>'(R)',       # registered
            '©' =>'(C)',       # copyright
            '…'=>'...'        # ellipsis
        );
        foreach($glyphs as $f=>$r){
            $text = str_replace($f,$r,$text);
        }
        return $text;
    }
    function detextile_process_lists($html) {
          $list = "";    
        $licz= 0;
        $licz_u = 0;
        $html = preg_split("/(<.*>)/U",$html,-1,PREG_SPLIT_DELIM_CAPTURE);       
        foreach($html as $line){         
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
    function processTag($matches) {
        list($all,$tag,$atts,$content) = $matches;
        $a = $this->splat($atts);
        $phr = array(
        'em'=>'_',
        'i'=>'__',
        'b'=>'**',
        'strong'=>'*',
        'cite'=>'??',
        'del'=>'-',
        'ins'=>'+',
        'u'=>'+',
        'sup'=>'^',
        'sub'=>'~',
        'span'=>'%',
        'code'=>'@'
        );
        $blk = array('p','h1','h2','h3','h4','h5','h6');
        if(isset($phr[$tag])) {
            return $phr[$tag].$this->sci($a).$content.$phr[$tag];
        } elseif($tag=='blockquote') {
            return 'bq.'.$this->sci($a).' '.$content;
        } elseif($tag=='center') {
            return 'p=.'.$this->sci($a).' '.$content;
        } elseif(in_array($tag,$blk)) {
            return $tag.$this->sci($a).'. '.$content;
        } elseif ($tag=='a') {
            $t = $this->filterAtts($a,array('href','title'));
            $out = '"'.$content;
            $out.= (isset($t['title'])) ? '('.preg_replace(array("/\(/","/\)/"), array("[","]"), $t['title']).')' :'';
            $out.= '":'.$t['href'];
            return $out;
        } elseif ($tag=='img') {
            $t = $this->filterAtts($a,array('src','alt'));
            $out = '!'.($t['src']);
            $out.= (isset($t['alt'])) ?'('.preg_replace(array("/\(/","/\)/"), array("[","]"), $t['alt']).')' :'';
            $out.= '!';
            return $out;
        } else {
            return $all;
        }
    }
// -------------------------------------------------------------
    function filterAtts($atts,$ok)
    {
        foreach($atts as $a) {
            if(in_array($a['name'],$ok)) {
                if($a['att']!='') {
                $out[$a['name']] = $a['att'];
                }
            }
        }
#        dump($out);
        return $out;
    }
// -------------------------------------------------------------
    function sci($a)
    {
        $out = '';
        foreach($a as $t){
            $out.= ($t['name']=='class') ? '(='.$t['att'].')' : '';
            $out.= ($t['name']=='id') ? '[='.$t['att'].']' : '';
            $out.= ($t['name']=='style') ? '{='.$t['att'].'}' : '';
            $out.= ($t['name']=='cite') ? ':'.$t['att'] : '';
            if ($t['name']=='align')
                if ($t['att'] == "left")
                    $out.= '/#';
                elseif ($t['att'] == "right")
                    $out.= '#\\';
                elseif ($t['att'] == "center")
                    $out.= '=';
                elseif ($t['att'] == "justify")
                    $out.= '/##\\';
        }
        return $out;
    }
// -------------------------------------------------------------
    function splat($attr)  // returns attributes as an array
    {
        $arr = array();
        $atnm = '';
        $mode = 0;
        while (strlen($attr) != 0){
            $ok = 0;
            switch ($mode) {
                case 0: // name
                    if (preg_match('/^([a-z]+)/i', $attr, $match)) {
                        $atnm = $match[1]; $ok = $mode = 1;
                        $attr = preg_replace('/^[a-z]+/i', '', $attr);
                    }
                break;
                case 1: // =
                    if (preg_match('/^\s*=\s*/', $attr)) {
                        $ok = 1; $mode = 2;
                        $attr = preg_replace('/^\s*=\s*/', '', $attr);
                    break;
                    }
                    if (preg_match('/^\s+/', $attr)) {
                        $ok = 1; $mode = 0;
                        $arr[] =
array('name'=>$atnm,'whole'=>$atnm,'att'=>$atnm);
                        $attr = preg_replace('/^\s+/', '', $attr);
                    }
                break;
                case 2: // value
                    if (preg_match('/^("[^"]*")(\s+|$)/', $attr,
$match)) {
                        $arr[]=array('name'
=>$atnm,'whole'=>$atnm.'='.$match[1],
                                'att'=>str_replace('"','',$match[1]));
                        $ok = 1; $mode = 0;
                        $attr = preg_replace('/^"[^"]*"(\s+|$)/', '',
$attr);
                    break;
                    }
                    if (preg_match("/^('[^']*')(\s+|$)/", $attr,
$match)) {
                        $arr[]=array('name'
=>$atnm,'whole'=>$atnm.'='.$match[1],
                                'att'=>str_replace("'",'',$match[1]));
                        $ok = 1; $mode = 0;
                        $attr = preg_replace("/^'[^']*'(\s+|$)/", '',
$attr);
                    break;
                    }
                    if (preg_match("/^(\w+)(\s+|$)/", $attr, $match)) {
                        $arr[]=
                            array('name'=>$atnm,'whole'=>$atnm.'="'.$match[1].'"',
                                'att'=>$match[1]);
                        $ok = 1; $mode = 0;
                        $attr = preg_replace("/^\w+(\s+|$)/", '',
$attr);
                    }
                break;
            }
            if ($ok == 0){
                $attr = preg_replace('/^\S*\s*/', '', $attr);
                $mode = 0;
            }
        }
        if ($mode == 1) $arr[] =
                array
('name'=>$atnm,'whole'=>$atnm.'="'.$atnm.'"','att'=>$atnm);
        return $arr;
    }
// -------------------------------------------------------------
    function cmap() {
        $f = 0xffff;
        
        $cmap = array(
        160,  255,  0, $f,
        402,  402,  0, $f,
        913,  929,  0, $f,
        931,  937,  0, $f,
        945,  969,  0, $f,
        977,  978,  0, $f,
        982,  982,  0, $f,
        8226, 8226, 0, $f,
        8230, 8230, 0, $f,
        8242, 8243, 0, $f,
        8254, 8254, 0, $f,
        8260, 8260, 0, $f,
        8465, 8465, 0, $f,
        8472, 8472, 0, $f,
        8476, 8476, 0, $f,
        8482, 8482, 0, $f,
        8501, 8501, 0, $f,
        8592, 8596, 0, $f,
        8629, 8629, 0, $f,
        8656, 8660, 0, $f,
        8704, 8704, 0, $f,
        8706, 8707, 0, $f,
        8709, 8709, 0, $f,
        8711, 8713, 0, $f,
        8715, 8715, 0, $f,
        8719, 8719, 0, $f,
        8721, 8722, 0, $f,
        8727, 8727, 0, $f,
        8730, 8730, 0, $f,
        8733, 8734, 0, $f,
        8736, 8736, 0, $f,
        8743, 8747, 0, $f,
        8756, 8756, 0, $f,
        8764, 8764, 0, $f,
        8773, 8773, 0, $f,
        8776, 8776, 0, $f,
        8800, 8801, 0, $f,
        8804, 8805, 0, $f,
        8834, 8836, 0, $f,
        8838, 8839, 0, $f,
        8853, 8853, 0, $f,
        8855, 8855, 0, $f,
        8869, 8869, 0, $f,
        8901, 8901, 0, $f,
        8968, 8971, 0, $f,
        9001, 9002, 0, $f,
        9674, 9674, 0, $f,
        9824, 9824, 0, $f,
        9827, 9827, 0, $f,
        9829, 9830, 0, $f,
        338,  339,  0, $f,
        352,  353,  0, $f,
        376,  376,  0, $f,
        710,  710,  0, $f,
        732,  732,  0, $f,
        8194, 8195, 0, $f,
        8201, 8201, 0, $f,
        8204, 8207, 0, $f,
        8211, 8212, 0, $f,
        8216, 8218, 0, $f,
        8218, 8218, 0, $f,
        8220, 8222, 0, $f,
        8224, 8225, 0, $f,
        8240, 8240, 0, $f,
        8249, 8250, 0, $f,
        8364, 8364, 0, $f
        );
        
        return $cmap;
    }
// -------------------------------------------------------------
    function decode_high($text) {
        $cmap = $this->cmap();
        return mb_decode_numericentity($text, $cmap, "UTF-8");
    }
}
?>

<html>
    <head>
        <title>Html to Textile Converter - Html2Textile</title>
        <meta http-equiv="Cotent-Type" content="text/html;charset=utf-8" />
        <link rel="stylesheet" type="text/css" media="screen" href="http://txstyle.org/css/bootstrap3min.css" />
    </head>

    <body>

    <div class="container">
      <div class="row">
        <div class="col-md-10">

        <h1><b>HTML to Textile Converter</b></h1>
    <hr/>
        <p><b>This will convert simple HTML data into Textile.</b></p>
        <p><b>Enter HTML data here:</b><br/><br/>

        <?php
            if (isSet($_POST['html']))
            {
                //usuwam slashe
                $html = stripslashes($_POST['html']);
                $html2textile = new html2textile;
                $textile = $html2textile->detextile($html);
            
            }
            ?>
        <form action="<?php $_SERVER['SCRIPT_NAME']?>" method='post'>
            <p style='text-align:left;'>
                <textarea name='html' style='width:100%; background-color:#FAFAE0; border:1px solid silver;' cols='80' rows='23'><?php if (isset($html)){echo $html;} ?></textarea>
            </p>
            <p style='text-align:left;'>
                <input name='html2textile' value='Convert to Textile!' class='button' style='font-size:120%; font-weight:bold;' type='submit' />
            </p>
        </form>
        
        <?php
            if (isset($textile)){
                echo "<textarea name='textile' style='width:100%; background-color:#FFFFFF; border:1px solid silver;' cols='80' rows='23'>$textile</textarea>";
            }
        ?>


        </div><!-- col-md-10-->
      </div><!-- row-->
    </div><!-- fluid-container-->

    </body>
</html>