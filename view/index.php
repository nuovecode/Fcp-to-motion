<?php $modulePath = "../modules/blocks/"?>
<?php $relateRoot = "final-cut-fast-titles"?>
<?php $relateName = "Fast titles"?>

<!DOCTYPE html>
<html>
<head>
    <?php include_once("view/frontend/html/head.php"); ?>
</head>
<body class="fcp-to-motion-index">
  <?php if(file_exists( $modulePath . "script/final-cut-to-motion.php"))  include_once $modulePath . "script/final-cut-to-motion.php"; ?>
  <?php if(file_exists("../modules/menu.php"))  include_once "../modules/menu.php"; ?>

  <div class="main">
      <div class="container">
          <div class="intro">
              <a href="http://www.ilgattohanuovecode.it/">
                  <img src="http://www.ilgattohanuovecode.it/blog/templates/nuovecode/images/logo.png" alt="il gatto ha nuove code" class="logo" />
              </a>
              <h1>Final Cut to Motion</h1>
              <h2>Motion 5 roundtrip from Final Cut Pro X</h2>
          </div>
          <div class="box fcp-upload">
              <p class="legend">Upload a file with .fcpxml extension (version 1.4) and click "export file":</p>
              <form enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
                  <input name="xml" id="file-1" class="inputfile inputfile-1" data-multiple-caption="{count} files selected" multiple type="file" class="inputfile"/>
                  <label for="file-1"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="17" viewBox="0 0 20 17">
                          <path d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z"/></svg>
                      <span>Upload a file .fcpxml</span>
                  </label>
                  <input type="submit" value="Export file" name="submit" class="done" />
              </form>
          </div>
      </div>
  </div>
  <?php include_once("view/frontend/html/blocks.php"); ?>
  <footer class="footer">
      <?php if(file_exists("../modules/footer.php"))  include_once "../modules/footer.php"; ?>
  </footer>
  <script src="view/frontend/js/upload.js"></script>
  <script type="text/javascript">
      $("input.done").click(function() {
         $('.alert-danger').remove();
      });
  </script>
</body>
</html>