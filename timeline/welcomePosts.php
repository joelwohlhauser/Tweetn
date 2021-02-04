<?php
// welcome posts
function EchoWelcomePosts($_userJoinedDate) {
	echo "<div class='container-bullets'><div class='bullet'></div><div class='vertical-line'></div></div>";
	echo "<div class='container-content'>";
	  echo "<p class='timeline-username'>Tweetn:</p>";
	  echo "<p class='timeline-content'>WELCOME TO TWEETN!</p>";
	  echo "<p class='timeline-time'>" . $_userJoinedDate . "</p>";
	echo "</div>";
	
	echo "<div class='container-bullets'><div class='bullet'></div><div class='vertical-line'></div></div>";
	echo "<div class='container-content'>";
	  echo "<p class='timeline-username'>Tweetn:</p>";
	  echo "<p class='timeline-content'>Tweetn is a social network. You can chat with other people and write posts, that all your followers will see.</p>";
	  echo "<p class='timeline-time'>" . $_userJoinedDate . "</p>";
	echo "</div>";
	
	echo "<div class='container-bullets'><div class='bullet'></div><div class='vertical-line'></div></div>";
	echo "<div class='container-content'>";
	  echo "<p class='timeline-username'>Tweetn:</p>";
	  echo "<p class='timeline-content'>If you want to chat with us, just search for 'Tweetn' and follow us. After that, you can send us messages. <br> We will answer as fast as possible.</p>";
	  echo "<p class='timeline-time'>" . $_userJoinedDate . "</p>";
	echo "</div>";
	
	echo "<div class='container-bullets'><div class='bullet'></div><div class='vertical-line'></div></div>";
	echo "<div class='container-content'>";
	  echo "<p class='timeline-username'>Tweetn:</p>";
	  echo "<p class='timeline-content'>We hope you will enjoy using Tweetn!</p>";
	  echo "<p class='timeline-time'>" . $_userJoinedDate . "</p>";
	echo "</div>";
	
	echo "<div class='container-bullets'><div class='bullet'></div><div class='vertical-line'></div></div>";
	echo "<div class='container-content'>";
	  echo "<p class='timeline-username'>Tweetn:</p>";
	  echo "<p class='timeline-content'>And please be nice to other people on Tweetn :)</p>";
	  echo "<p class='timeline-time'>" . $_userJoinedDate . "</p>";
	echo "</div>";
}
			
?>