<html>
    <head>
      <title>Teste</title>
    </head>
    <body>
        <form action="action.php" id='form' class='form' method="POST" enctype="multipart/form-data">
            <div class="retorno"></div>
            <p><img src="" id='imagem' height="200" style="display: none;"></p>
            <span>Imagem</span>
            <input type="file" name="imagem" id="file">
            <p>
                <label>Texto</label>
                <input id="nome" name="nome" type="text" >
            </p>
            <button type="submit">Gravar</button>
        </form>
     
        <script type="text/javascript" src="//code.jquery.com/jquery-3.3.1.min.js"></script>
        <script type="text/javascript" src="custom.js"></script>
    </body>
</html>