<?php
require_once './includes/__navbar__.php';
require_once './includes/__side_bar__.php';
?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>Settings</h1>
  </section>

  <section class="content">
    <div class="container-fluid">

      <form id="settingsForm" enctype="multipart/form-data" autocomplete="off">
        <div class="card card-primary">
          <div class="card-header"><h3 class="card-title">Site Info</h3></div>
          <div class="card-body">
            <div class="form-group">
              <label for="siteName">Site Name</label>
              <input type="text" class="form-control" id="siteName" placeholder="Enter site name" value="My E-Commerce Store">
            </div>
            <div class="form-group">
              <label for="siteDesc">Site Description</label>
              <textarea class="form-control" id="siteDesc" rows="2" placeholder="Short site description">Your one-stop shop for everything</textarea>
            </div>

            <div class="form-group">
              <label for="siteLogo">Site Logo</label>
              <input type="file" class="form-control-file" id="siteLogo" accept="image/*" />
              <div class="mt-2" id="logoPreviewWrapper" style="width:120px; height:120px; border-radius:10px; box-shadow:0 0 10px rgba(255,255,255,0.7); background:#fff; display:flex; justify-content:center; align-items:center;">
                <img id="logoPreview" src="https://via.placeholder.com/100?text=Logo" alt="Logo Preview" style="max-width:100%; max-height:100%; border-radius:10px;">
              </div>
            </div>

            <div class="form-group">
              <label for="footerTagline">Footer Tagline</label>
              <input type="text" class="form-control" id="footerTagline" placeholder="Footer tagline" value="© 2025 Your Company. All rights reserved.">
            </div>
          </div>
        </div>

        <div class="card card-secondary">
          <div class="card-header"><h3 class="card-title">Social Links</h3></div>
          <div class="card-body">
            <div class="form-group">
              <label for="facebookLink">Facebook URL</label>
              <input type="url" class="form-control" id="facebookLink" placeholder="https://facebook.com/yourpage" value="https://facebook.com/hridoy">
            </div>
            <div class="form-group">
              <label for="twitterLink">Twitter URL</label>
              <input type="url" class="form-control" id="twitterLink" placeholder="https://twitter.com/yourhandle" value="https://twitter.com/hridoy">
            </div>
            <div class="form-group">
              <label for="instagramLink">Instagram URL</label>
              <input type="url" class="form-control" id="instagramLink" placeholder="https://instagram.com/yourhandle" value="https://instagram.com/hridoy">
            </div>
            <div class="form-group">
              <label for="linkedinLink">LinkedIn URL</label>
              <input type="url" class="form-control" id="linkedinLink" placeholder="https://linkedin.com/in/yourprofile" value="https://linkedin.com/in/hridoy">
            </div>
          </div>
        </div>

        <div class="card card-info">
          <div class="card-header"><h3 class="card-title">Contact Info</h3></div>
          <div class="card-body">
            <div class="form-group">
              <label for="contactPhone">Phone Number</label>
              <input type="text" class="form-control" id="contactPhone" placeholder="+8801234567890" value="+8801234567890">
            </div>
            <div class="form-group">
              <label for="contactAddress">Address</label>
              <textarea class="form-control" id="contactAddress" rows="2" placeholder="Store address">123 Main St, Dhaka, Bangladesh</textarea>
            </div>
          </div>
        </div>

        <div class="card card-warning">
          <div class="card-header"><h3 class="card-title">Admin Info & Password</h3></div>
          <div class="card-body">
            <div class="form-group">
              <label for="adminName">Admin Name</label>
              <input type="text" class="form-control" id="adminName" placeholder="Admin Name" value="Hridoy">
            </div>
            <div class="form-group">
              <label for="adminEmail">Admin Email</label>
              <input type="email" class="form-control" id="adminEmail" placeholder="admin@example.com" value="admin@example.com">
            </div>
            <div class="form-group position-relative">
              <label for="adminPassword">New Password</label>
              <input type="password" class="form-control" id="adminPassword" placeholder="Enter new password">
              <button type="button" class="btn btn-sm btn-secondary position-absolute" style="top: 30px; right: 10px;" id="togglePass">Show</button>
            </div>
            <div class="form-group position-relative">
              <label for="adminPasswordConfirm">Confirm Password</label>
              <input type="password" class="form-control" id="adminPasswordConfirm" placeholder="Confirm new password">
              <button type="button" class="btn btn-sm btn-secondary position-absolute" style="top: 30px; right: 10px;" id="togglePassConfirm">Show</button>
            </div>
          </div>
        </div>

        <button type="submit" class="btn btn-primary mb-4">Save Settings</button>
      </form>

    </div>
  </section>
</div>

<?php require_once './includes/__footer__.php'; ?>

<script>
  const logoInput = document.getElementById('siteLogo');
  const logoPreview = document.getElementById('logoPreview');

  logoInput.addEventListener('change', e => {
    const file = e.target.files[0];
    if(!file) return;

    if(!file.type.startsWith('image/')){
      toastr.error('Only image files allowed!');
      logoInput.value = '';
      logoPreview.src = 'https://via.placeholder.com/100?text=Logo';
      return;
    }

    const reader = new FileReader();
    reader.onload = function(e) {
      logoPreview.src = e.target.result;
    }
    reader.readAsDataURL(file);
  });

  function togglePassword(inputId, btnId) {
    const input = document.getElementById(inputId);
    const btn = document.getElementById(btnId);
    btn.addEventListener('click', () => {
      if(input.type === 'password'){
        input.type = 'text';
        btn.innerText = 'Hide';
      } else {
        input.type = 'password';
        btn.innerText = 'Show';
      }
    });
  }

  togglePassword('adminPassword', 'togglePass');
  togglePassword('adminPasswordConfirm', 'togglePassConfirm');

  document.getElementById('settingsForm').addEventListener('submit', e => {
    e.preventDefault();

    const formData = new FormData();

    formData.append('siteName', document.getElementById('siteName').value.trim());
    formData.append('siteDesc', document.getElementById('siteDesc').value.trim());
    formData.append('footerTagline', document.getElementById('footerTagline').value.trim());
    formData.append('facebookLink', document.getElementById('facebookLink').value.trim());
    formData.append('twitterLink', document.getElementById('twitterLink').value.trim());
    formData.append('instagramLink', document.getElementById('instagramLink').value.trim());
    formData.append('linkedinLink', document.getElementById('linkedinLink').value.trim());
    formData.append('contactPhone', document.getElementById('contactPhone').value.trim());
    formData.append('contactAddress', document.getElementById('contactAddress').value.trim());
    formData.append('adminName', document.getElementById('adminName').value.trim());
    formData.append('adminEmail', document.getElementById('adminEmail').value.trim());
    formData.append('adminPassword', document.getElementById('adminPassword').value);
    formData.append('adminPasswordConfirm', document.getElementById('adminPasswordConfirm').value);

    if(logoInput.files[0]){
      formData.append('siteLogo', logoInput.files[0]);
    }

    // Here you can send formData via AJAX to server for saving

    toastr.success('Settings saved! (Demo only, add your backend logic)');
  });
</script>
