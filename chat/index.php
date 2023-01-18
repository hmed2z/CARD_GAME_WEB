<div id="wrapper">
    <div id="chatbox">
        <?php
        if (file_exists("log.html") && filesize("log.html") > 0) {
            $contents = file_get_contents("log.html");
            echo $contents;
        }
        ?>
    </div>

    <form name="message" action="">
        <input name="usermsg" type="text" id="usermsg" placeholder="Chat with your opponent" />
        <input name="submitmsg" type="submit" id="submitmsg" value="Send" />
    </form>
</div>