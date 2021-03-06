<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="initial-scale=1">
<title>to-textile - an HTML to Textile converter written in javascript</title>
<link rel="stylesheet" href="normalize.css"></link>
<style>
  .cf:before,
  .cf:after {
      content: " ";
      display: table;
  }

  .cf:after {
      clear: both;
  }

  .cf {
      *zoom: 1;
  }

  * {
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
  }

  body {
    background-color: #ccc;
    margin: 0 auto;
    font-size: 14px;
    font-family: sans-serif;
    line-height: 1.4;
    color: #333;
  }

  header {
    padding: 1em;
    overflow: hidden;
    background-color: #fff;
  }

  footer {
    text-align: center;
    color: #666;
    text-shadow: 0 1px 0 #ddd;
  }

  a,
  a:visited {
    font-weight: 700;
    color: #4A90E2;
    text-decoration: none;
  }

  h1 {
    float: left;
    margin: 0;
    font-size: 1em;
  }

  h2 {
    color: #fff;
    margin-bottom: 0;
  }

  .col {
    padding: 0 10px;
  }

  textarea {
    width: 100%;
    height: 600px;
    margin: 0;
    padding: .5em;
    overflow: auto;
    border: none;
    background-color: #fff;
    font-family: courier, monospace;
    font-size: inherit;
    color: inherit;
  }

  #input {
    background: #333;
    color: #fff;
  }

  .toolbar {
    padding-top: 5px;
    padding-bottom: 5px;
    background-color: #e6e6e6;
  }

  @media (min-width: 768px) {
    body {
      font-size: 16px;
    }

    .col {
      float: left;
      width: 50%;
      padding: 0 15px;
    }

    .row {
      padding-right: 15px;
      padding-left: 15px;
    }
  }
</style>
<script src="js/to-textile.js"></script>
<script>
(function () {
  var input, output;

  function updateOutput() {
    output.value = toTextile(input.value, {  });
  }

  document.addEventListener("DOMContentLoaded", function(event) {
    input = document.getElementById('input');
    output = document.getElementById('output');

    input.addEventListener('input', updateOutput, false);
    input.addEventListener('keydown', updateOutput, false);

    updateOutput();
  });
})();
</script>
</head>
<body>
  <header>
    <h1>to-textile</h1>
    <a style="float: right" href="https://github.com/cmroanirgo/to-textile">Source on GitHub</a>
  </header>
  <div class="row cf toolbar">
    <div class="col">
    </div>
  </div>
  <div class="row cf">
    <div class="col">
      <h2>HTML</h2>
      <textarea id="input" cols="100" rows="10"><h3>Some HTML</h3>

<p>Hello world! This demonstrates <a href="https://github.com/cmroanirgo/to-textile">to-textile</a> – an HTML to Textile converter.</p>

<p style="text-align:right">Right Aligned Paragraph</p>
<p>An ordinary paragraph with an <img src="/images/image.jpg"></p>

<ol class="nav">
  <li>Number 1</li>
  <li>Nested List:
	<ul style="color:red">
	  <li>Item 1</li>
	  <li>Item Two</li>
	</ul>
	</li>
	<li>Number 3</li>
</ol>
<p>Another paragraph with some <code>inline code</code>.</p>
<blockquote>Be ye perfect</blockquote>

</textarea>
    </div>

    <div class="col">
      <h2>Textile</h2>
      <textarea readonly id="output"></textarea>
    </div>
  </div>
  <footer>
    <p>to-textile is copyright &copy; 2017 <a href="http://github.com/cmroanirgo/">cmroanirgo</a> and is released under the MIT licence. See also <a href="compare.html">Compare</a></p>
  </footer>
</body>
</html>   