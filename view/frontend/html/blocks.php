<?php $repositoryName = 'Fcp-to-motion'?>
<?php $url = "http://www.ilgattohanuovecode.it/tool/final-cut-round-trip/"?>

<div id="help">
   <div class="container">
       <div class="col-md-5">
           <!-- APP SUMMARY-->
         <p class="title"><?php echo utf8_decode(' What is Final cut motion?') ?></p>
         <p><?php echo utf8_decode('Final cut to motion is a free app for sending the whole timeline from Final Cut to Motion.') ?>
             <?php echo utf8_decode('Just export your project as .fcpxml (File > Export XML)') ?>,
             <?php echo utf8_decode('upload it above and download a motion project.') ?>
             <br><br><?php echo utf8_decode('It is still a beta version, and we plan to improve it.') ?></p>
           <!-- SHARE BUTTONS ($url)-->
           <?php if(file_exists( $modulePath . "share-buttons.php"))  include_once $modulePath . "share-buttons.php"; ?>
      </div>
      <div class="col-md-7">
          <!-- SHARE BUTTONS ($repositoryName)-->
          <?php if(file_exists( $modulePath . "help-us.php"))  include_once $modulePath . "help-us.php"; ?>
          <!-- PAYPAL-->
          <?php if(file_exists( $modulePath ."paypal-donate.php"))  include_once $modulePath . "paypal-donate.php"; ?>
      </div>
      <div class="clearfix"></div>
   </div>
</div>

<div id="about">
    <div class="container">
        <div class="col-md-3">
            <p class="title">Supports:</p>
            <ul>
                <li>Video Clips</li>
                <li>Images</li>
                <li>Secondary Storylines</li>
                <li>Inspector parameters</li>
            </ul>
        </div>
        <div class="col-md-5">
            <p class="title">We would like to implement soon:</p>
            <ul>
                <li>Titles</li>
                <li>Generators</li>
                <li>Retiming</li>
                <li>Keyframes</li>
                <li>Audio Clips</li>
                <li>Markers</li>
                <li>Freeze Frames</li>
                <br>
                <strong style="color: #d3394c;">.. Any Ideas? :-)</strong>
            </ul>
        </div>
        <div class="col-md-4">
            <p class="title">Doesn't support:</p>
            <ul>
                <li>Effects</li>
                <li>Color</li>
                <li>Transitions</li>
                <li>Compound Clips</li>
                <li>Multicam clips</li>
            </ul>
            <br>
            <em>Features not found in Motion</em>
        </div>
    </div>
</div>


<div id="social">
   <div class="container">
       <!-- YOUTUBE -->
       <?php if(file_exists( $modulePath . "get-in-touch.php")): ?>
       <div class="col-md-6">
           <div class="youtube">
               <iframe width="560" height="315" src="https://www.youtube.com/embed/cWEKfqLBodQ" frameborder="0" allowfullscreen></iframe>
           </div>
       </div>
       <?php endif; ?>
       <!-- CONTACTS -->
       <?php if(file_exists( $modulePath . "get-in-touch.php"))  include_once $modulePath . "get-in-touch.php"; ?>
       <!-- SHARE BUTTONS ($url)-->
       <?php if(file_exists($modulePath ."links.php"))  include_once $modulePath . "links.php"; ?>
   </div>
</div>

</div>



