<div class="row">
  <div class="col-md-8">
    <div class="alert alert-success" role="alert">
      <h4 class="alert-heading">Bootstrap Integration Complete!</h4>
      <p><?php echo \App\Core\I18n::t('app_running'); ?></p>
      <hr>
      <p class="mb-0 text-muted"><?php echo \App\Core\I18n::t('edit_home'); ?></p>
    </div>
    
    <h3>Bootstrap Components Demo</h3>
    <p>This application now uses <strong>Bootstrap 5.3.2</strong> framework alongside the existing custom styles.</p>
    
    <div class="row g-3">
      <div class="col-sm-6">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Responsive Grid</h5>
            <p class="card-text">Bootstrap's grid system provides responsive layouts.</p>
            <a href="#" class="btn btn-primary">Learn More</a>
          </div>
        </div>
      </div>
      <div class="col-sm-6">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Components</h5>
            <p class="card-text">Rich set of UI components ready to use.</p>
            <a href="#" class="btn btn-outline-secondary">Explore</a>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-md-4">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Framework Features</h5>
      </div>
      <div class="card-body">
        <ul class="list-group list-group-flush">
          <li class="list-group-item d-flex justify-content-between align-items-center">
            Responsive Design
            <span class="badge bg-success rounded-pill">✓</span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            Mobile-First
            <span class="badge bg-success rounded-pill">✓</span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            Custom Styling
            <span class="badge bg-success rounded-pill">✓</span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            Bootstrap Integration
            <span class="badge bg-primary rounded-pill">New!</span>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
