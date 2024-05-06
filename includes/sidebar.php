<li class="nav-item dropdown has-arrow main-drop" style="display: flex; justify-content: flex-end; align-items: center; padding-top: 10px;">
  <a href="#" class="dropdown-toggle nav-link" data-bs-toggle="dropdown">
    <span class="user-img">
      <img src="assets/img/pholder.png" alt="" style="vertical-align: middle;">
      <span class="status online"></span>
    </span>
    <span style="margin-right: 5px;"><?php echo $_SESSION['name']; ?></span>
  </a>
  <div class="dropdown-menu">
    <a class="dropdown-item" href="profile.php"><i data-feather="user" class="me-1"></i> Profile</a>
    <a class="dropdown-item" href="logout.php"><i data-feather="log-out" class="me-1"></i> Logout</a>
  </div>
</li>