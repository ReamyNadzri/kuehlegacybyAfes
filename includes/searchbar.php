<form class="search-form-custom" method="GET" action='<?php echo $root_path; ?>recipes/kuehListing.php'>
    <i class="bi bi-search search-icon-custom"></i>
    <input type="text" name="search" class="search-input-custom" placeholder="Cari resipi atau bahan..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
</form>