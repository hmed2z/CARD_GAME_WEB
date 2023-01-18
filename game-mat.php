<?php
session_start();
$errors = [];
$username = "";
// connect to database
$db = mysqli_connect('localhost', 'root', '', 'game');


//REGISTER USER
if (isset($_POST['signup'])) {

    $username = mysqli_real_escape_string($db, trim($_POST['username']));
    $email = mysqli_real_escape_string($db, trim($_POST['email']));
    $password = mysqli_real_escape_string($db, trim($_POST['password']));
    $experience = mysqli_real_escape_string($db, trim($_POST['experience']));
    // validate form
    if (empty($username)) {
        array_push($errors, "Username is required");
    }
    if (empty($username)) {
        array_push($errors, "Email is required");
    }
    if (empty($password)) {
        array_push($errors, "Password is required");
    }

    $query = "SELECT * FROM users WHERE username ='" . $username . "'";
    $result = mysqli_query($db, $query) or die('Error1: Error querying the database');
    if (mysqli_num_rows($result) > 0) {
        echo "Username already in use. Use another name<br>";
    } else {

        if (sizeof($errors) === 0) {
            $password = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO users (username, email, password, experience) VALUES('" . $username . "', '" . $email . "', '" . $password . "', '" . $experience . "')";
            $insert = mysqli_query($db, $query) or die('Error2: Error querying the database.');
            echo "<p><span>Your account has been created successfully</span>.</p>";
            echo "<p>Here are your details:<br>" .
                "Username: <span>" . $username . "</span><br>" .
                "Email: <span>" . $email . "</span></p>";
            echo "<p>You can now <a href=\"index.html\">login</a></p>";
        }
    }
}

// LOG USER IN
if (isset($_POST['login'])) {
    // Get username and password from login form
    $username = mysqli_real_escape_string($db, trim($_POST['username']));
    $password = mysqli_real_escape_string($db, trim($_POST['password']));
    // validate form
    if (empty($username)) {
        array_push($errors, "Username or Email is required");
    }
    if (empty($password)) {
        array_push($errors, "Password is required");
    }

    // if no error in form, log user in
    if (count($errors) === 0) {
        $sql = "SELECT password FROM users WHERE username='$username' OR email='$username' ";
        //$sql = "SELECT password FROM users WHERE username='$username'";
        $results = mysqli_query($db, $sql);

        if (mysqli_num_rows($results) === 0) { //User is not on our database
            array_push($errors, "You are not registered to our site!");
        }
        $rows = mysqli_fetch_assoc($results);
        if ($rows) {
            $old_pswd = $rows['password'];

            if (password_verify($password, $old_pswd)) {
                $_SESSION['username'] = $username;

                function output($card_arr, $id)
                {
                    return '<img src="images/' . $card_arr . '" alt="' . $card_arr . '" id="' . $id . '"><br>';
                }
                function store($file_to, $what_to)
                {
                    file_put_contents($file_to, $what_to, FILE_APPEND | LOCK_EX);
                }

                //Code to dynamically load cards
                if (!file_exists('deck.html')) {
                    function randomGen($min, $max, $qnty)
                    {
                        $numbers = range($min, $max);
                        shuffle($numbers);
                        return array_slice($numbers, 0, $qnty);
                    }
                    $array1 = array();
                    $array2 = array();
                    $court = array();
                    $rand = randomGen(1, 55, 55);
                    $all_cards = $rand;
                    for ($i = 0; $i < 16; $i++) {
                        if ($i < 8) {
                            array_push($array1, $rand[$i]);
                        } else {
                            array_push($array2, $rand[$i]);
                        }
                        array_shift($rand);
                    }
                    for ($k = 0; $k < 2; $k++) {
                        array_push($court, $rand[$k]);
                        array_shift($rand);
                    }

                    $cards = glob('images/*');
                    $output0 = '';
                    $output1 = '';
                    $output2 = '';
                    $output3 = '';
                    $output4 = '';


                    for ($j = 0; $j < count($cards); $j++) {
                        $card = basename($cards[$j]);
                        $explode = explode('.', $card);
                        $str = explode('-', $explode[0]);
                        if (in_array(@$str[1], $all_cards)) {
                            $output0 .= output($card, @$str[1]);
                        }
                        if (in_array(@$str[1], $court)) {
                            $output3 .= output($card, @$str[1]);
                        }
                        if (in_array(@$str[1], $array1)) {
                            $output1 .= output($card, @$str[1]);
                        } elseif (in_array(@$str[1], $array2)) {
                            $output2 .= output($card, @$str[1]);
                        } else {
                            if ($card !== 'carte_Autres_3.png') {
                                $output4 .= output($card, @$str[1]);
                            }
                        }
                    }
                    store("deck.html", $output0);
                    store("player1.html", $output1);
                    store("player2.html", $output2);
                    store("court.html", $output3);
                    store("remaining_deck.html", $output4);

                }


                if (file_exists('who.txt')) {
                    $content = file_get_contents('who.txt');
                    $arr = explode('<br>', $content);
                    $oppo_data = explode(': ', $arr[count($arr) - 1]);
                    if (trim($oppo_data[0]) != trim($_SESSION['username'])) {
                        if ($oppo_data[1] != 'player1') {
                            $my_data = 'player1';
                        } else {
                            $my_data = 'player2';
                        }
                        $output5 = "<br>" . $_SESSION['username'] . ": " . $my_data;
                        store("who.txt", $output5);
                    }
                } else {
                    $my_data = 'player1';
                    $output5 =
                        "<br>" . $_SESSION['username'] . ": " . $my_data;
                    store("who.txt", $output5);
                }
                $data = explode('<br>', file_get_contents('who.txt'));
                $player = explode(': ', $data[count($data) - 1]);
                $_SESSION['player'] = $player[1];
?>

                <!DOCTYPE html>
                <html>

                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=0">
                    <link rel="stylesheet" href="css/game-style.css">
                    <title>The Game Play</title>
                </head>

                <body>
                    <nav>
                        <ul>
                            <li><button id="start">Play Now!</button></li>
                            <li><button><a href="logout.php">Logout</a></button></li>
                        </ul>
                    </nav>
                    <div id="container">
                        <div id="player1">
                            <div id="one" class="display">
                            </div>
                        </div>
                        <div id="player2">
                            <div id="two" class="display">
                            </div>
                        </div>
                        <div id="court-fold">
                            <div id="deckdiv">
                                <p>deck</p>
                                <div id="deck">

                                </div>
                            </div>
                            <div id="center">
                                <div id="position"></div>
                            </div>
                            <div id="me">
                                <p>My Score:</p>
                                <p id="myscore">0</p>
                            </div>
                            <div id="opponent">
                                <p>Opponent's Score:</p>
                                <p id="yourscore">0</p>
                            </div>
                            <div id="rotation">
                                <p id="turn">TO DO: SET TURN</p>
                            </div>
                        </div>
                        <div id="chat">
                            <?php
                            include('chat/index.php');
                            ?>
                        </div>
                    </div>

                    <script type="text/javascript" src="js/jquery-3.5.1.js"></script>
                    <script>
                        var player2 = [];
                        var decks = [];
                        $(document).ready(function() {
                            img_loaded = false;
                            hand1_loaded = true;

                            $("#start").click(function() {
                                //to determine who is player1 or player2
                                player = '<?php echo $_SESSION['player']; ?>';

                                if (player == 'player1') {
                                    $.ajax({
                                        url: "player1.html",
                                        cache: false,
                                        success: function(html) {
                                            let print = html.replaceAll('<br>', '');
                                            $("#one").html(print); 
                                        }

                                    });
                                    //load player2's hand
                                    $.ajax({
                                        url: "player2.html",
                                        cache: false,
                                        success: function(html) {
                                            player2 = html.split('<br>');
                                            let deck = '';
                                            //load 2 cards per time for the deck
                                            for (let i = 0; i < 8; i++) {
                                                id = player2[i].split('-')[1].split('.')[0];
                                                deck += '<img src="images/carte_Autres_3.png" alt="card back" id="' + id + '">';
                                            }
                                            $("#two").html(deck); 
                                        }
                                    });
                                } else {
                                    $.ajax({
                                        url: "player2.html",
                                        cache: false,
                                        success: function(html) {
                                            let print = html.replaceAll('<br>', '');
                                            $("#one").html(print); 
                                        }

                                    });
                                    //load player2's hand
                                    $.ajax({
                                        url: "player1.html",
                                        cache: false,
                                        success: function(html) {
                                            player2 = html.split('<br>');
                                            let deck = '';
                                            //load 2 cards per time for the deck
                                            for (let i = 0; i < 8; i++) {
                                                id = player2[i].split('-')[1].split('.')[0];
                                                deck += '<img src="images/carte_Autres_3.png" alt="card back" id="' + id + '">';
                                            }
                                            $("#two").html(deck);
                                        }
                                    });
                                }
                                //load player1's hand

                                //load court hand
                                $.ajax({
                                    url: "court.html",
                                    cache: false,
                                    success: function(html) {
                                        let print = html.replaceAll('<br>', '');
                                        $("#position").html(print); 
                                    }
                                });
                                img_loaded = true;
                                hand1_loaded = true;

                                //load deck
                                $.ajax({
                                    url: "remaining_deck.html",
                                    cache: false,
                                    success: function(html) {
                                        decks = html.split('<br>');
                                        let deck = '';
                                        //load 2 cards per time for the deck
                                        for (let i = 0; i < 2; i++) {
                                            id = decks[i].split('-')[1].split('.')[0];
                                            deck = '<img src="images/carte_Autres_3.png" alt="card back" id="' + id + '">';
                                            $("#deck").prepend(deck); 
                                        }

                                    }
                                });
                            });
                        });
                    </script>
                    <script type="text/javascript">
                   
                        $(document).ready(function() {
                            $("#submitmsg").click(function() {
                                var clientmsg = $("#usermsg").val();
                                $.post("chat/post.php", {
                                    text: clientmsg
                                });
                                $("#usermsg").val("");
                                return false;
                            });

                            function loadLog() {
                                var oldscrollHeight = $("#chatbox")[0].scrollHeight - 20; //Scroll height before the request

                                $.ajax({
                                    url: "chat/log.html",
                                    cache: false,
                                    success: function(html) {
                                        $("#chatbox").html(html); //Insert chat log into the #chatbox div

                                        //Auto-scroll           
                                        var newscrollHeight = $("#chatbox")[0].scrollHeight - 20; //Scroll height after the request
                                        if (newscrollHeight > oldscrollHeight) {
                                            $("#chatbox").animate({
                                                scrollTop: newscrollHeight
                                            }, 'normal'); //Autoscroll to bottom of div
                                        }
                                    }
                                });
                            }

                            setInterval(loadLog, 500);
                        });
                    </script>
                    <script>
                        function dragEnter(e) {
                            e.preventDefault();
                            e.target.classList.add('drag-over');
                        }

                        function dragOver(e) {
                            e.preventDefault();
                            e.target.classList.add('drag-over');
                        }

                        function dragLeave(e) {
                            e.target.classList.remove('drag-over');
                        }
                        setInterval(function() {
                            if (img_loaded) {
                                let items = document.querySelectorAll('#one img');
                                items.forEach(item => {
                                    item.addEventListener('dragstart', dragStart);
                                });

                                function dragStart(e) {
                                    e.dataTransfer.setData('text/plain', e.target.id);
                                    setTimeout(() => {
                                        e.target.classList.add('hide');
                                    }, 0);
                                }


                                /* drop targets */
                                const position = document.getElementById('position');

                                position.addEventListener('dragenter', dragEnter)
                                position.addEventListener('dragover', dragOver);
                                position.addEventListener('dragleave', dragLeave);
                                position.addEventListener('drop', drop);

                                setInterval(function() {
                                    const children = document.querySelectorAll('#position img');
                                    children.forEach(child => {
                                        child.addEventListener('drop', dropChild);
                                    });
                                }, 1000);

                                function dragLeave(e) {
                                    e.target.classList.remove('drag-over');
                                }


                                function drop(e) {
                                    e.target.classList.remove('drag-over');

                                    // get the draggable element
                                    const id = e.dataTransfer.getData('text/plain');
                                    const draggable = document.getElementById(id);
                                    //Remove first img
                                    e.target.removeChild(document.querySelector('#position img'));
                                    // add it to the drop target
                                    e.target.appendChild(draggable);

                                    // display the draggable element
                                    draggable.classList.remove('hide');
                                }

                                function dropChild(e) {
                                    e.target.classList.remove('drag-over');

                                    // get the draggable element
                                    const id = e.dataTransfer.getData('text/plain');
                                    const draggable = document.getElementById(id);
                                    //Remove first img
                                    $('#position')[0].removeChild(document.querySelector('#position img'));
                                    // add it to the drop target
                                    $('#position')[0].appendChild(draggable);

                                    // display the draggable element
                                    draggable.classList.remove('hide');
                                }
                            }
                            img_loaded = false;
                        }, 2500)
                    </script>
                    <script>
                        setInterval(function() {


                            if (hand1_loaded) {
                                deckLength = decks.length;

                                let items = document.querySelectorAll('#deck img');
                                items.forEach(item => {
                                    item.addEventListener('dragstart', dragStart);
                                });

                                function dragStart(e) {
                                    e.dataTransfer.setData('text/plain', e.target.id);
                                    document.getElementById(e.target.id).src = decks[0].split('src=\"')[1].split('\" ')[0];
                                    setTimeout(() => {
                                        e.target.classList.add('hide');
                                    }, 0);
                                }


                                /* drop targets */
                                const player1 = document.getElementById('player1');

                                player1.addEventListener('dragenter', dragEnter)
                                player1.addEventListener('dragover', dragOver);
                                player1.addEventListener('dragleave', dragLeave);
                                player1.addEventListener('drop', dropTo);


                                function dropTo(e) {
                                    e.target.classList.remove('drag-over');

                                    // get the draggable element
                                    const id = e.dataTransfer.getData('text/plain');
                                    const draggable = document.getElementById(id);

                                    // add it to the drop target
                                    $('#one').prepend(draggable);

                                    // display the draggable element
                                    draggable.classList.remove('hide');
                                    //remove the dropped  card from the deck
                                    decks.shift();
                                    //attach a new card to deck element
                                    if (decks.length < deckLength) {
                                        elemId = decks[1].split('-')[1].split('.')[0];
                                        if (decks.length == 1) {
                                            elemId = decks[0].split('-')[1].split('.')[0];
                                        }
                                        adding = '<img src="images/carte_Autres_3.png" alt="card back" id="' + elemId + '">';
                                        $('#deck').prepend(adding);
                                        $('#' + elemId)[0].addEventListener('dragstart', dragStart);
                                    }


                                }
                            }
                            hand1_loaded = false;
                        }, 2500)
                    </script>
                </body>

                </html>

<?php
            } else {
                array_push($errors, "Wrong Password");
                echo "Wrong Password<br>";
                echo "<p> Please Try Again <a href=\"index.html\">login</a></p>";
            }
        }
    }
} else {
    exit();
}
?>