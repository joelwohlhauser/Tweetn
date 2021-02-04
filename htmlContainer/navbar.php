<div class="nav">
  <div class="nav-header">
    <div class="nav-title">
      <a class="nav-Tweetn" href="../index.html">Tweetn</a>
    </div>
  </div>
  <div class="nav-btn">
    <label for="nav-check" class="container"  onclick="myFunction(this)">
      <div class="bar1"></div>
      <div class="bar2"></div>
      <div class="bar3"></div>
    </label>
  </div>
  <input type="checkbox" id="nav-check">
  <div class="nav-links">
    <a href="../timeline/timeline.php">Timeline</a>
    <a href="../chat/chat.php">Chat</a>
    <a href="../profile/searchProfile.php">Search</a>
    <a href="../profile/myProfile.php">Profile</a>
  </div>
</div>

<!-- Close button on navbar -->
<script>
function myFunction(x) {
  x.classList.toggle("change");
}
</script>
