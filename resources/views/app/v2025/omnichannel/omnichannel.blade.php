@extends('app.structure')
@section('content')
<style>
  /* Jezpro colors */
  :root {
    --jez-primary: #e30613;
    /* Jezpro red */
    --jez-secondary: #2a2a2a;
    /* Jezpro black/dark */
    --jez-success: #4caf50;
    /* Green */
    --jez-danger: #ff5722;
    /* Orange */
    --jez-light-gray: #f5f5f5;
    /* Light Gray */
  }

  /* General styling */
  body {
    background-color: #f8f9fa;
  }

  .card-3d {
    background: linear-gradient(135deg, white, #f9f9f9);
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    padding: 1.5rem;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }

  .card-3d:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
  }

  .table th {
    background-color: var(--jez-secondary);
    color: white;
  }

  .badge-shopee {
    background-color: var(--jez-danger);
    color: white;
  }

  .badge-tokopedia {
    background-color: var(--jez-success);
    color: white;
  }

  .nav-tabs .nav-link.active {
    background-color: var(--jez-primary);
    color: white;
  }

  .nav-tabs .nav-link {
    color: var(--jez-primary);
    border: 1px solid var(--jez-primary);
  }

  .nav-tabs .nav-link:hover {
    background-color: var(--jez-primary);
    color: white;
  }

  /* Filter styling */
  .filter-group {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background-color: var(--jez-light-gray);
    padding: 1rem;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  }

  .filter-group label {
    font-weight: bold;
    color: var(--jez-secondary);
  }

  .filter-group .form-control,
  .filter-group .form-select {
    border-radius: 5px;
    border: 1px solid var(--jez-primary);
    padding: 0.5rem;
    transition: all 0.3s ease;
  }

  .filter-group .form-control:focus,
  .filter-group .form-select:focus {
    box-shadow: 0 0 5px var(--jez-primary);
    border-color: var(--jez-primary);
  }

  .filter-group button {
    background-color: var(--jez-primary);
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 5px;
    transition: background-color 0.3s ease;
  }

  .filter-group button:hover {
    background-color: var(--jez-primary);
    opacity: 0.8;
  }

  .container {
    padding-bottom: 50px;
  }

  .tab-content {
    margin-top: 20px;
  }

  .pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 1rem 0;
  }

  .pagination li {
    margin: 0 5px;
  }

  .pagination a {
    color: var(--jez-primary);
    text-decoration: none;
    border: 1px solid var(--jez-primary);
    padding: 0.5rem 0.75rem;
    border-radius: 5px;
    transition: all 0.2s ease-in-out;
  }

  .pagination a:hover {
    background-color: var(--jez-primary);
    color: white;
  }

  .pagination .active a {
    background-color: var(--jez-primary);
    color: white;
  }

  .chart-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    padding: 1.5rem;
  }
</style>

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <!--begin::Subheader-->
  <div class="subheader py-2 py-lg-6 subheader-solid" id="kt_subheader">
    <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
      <!--begin::Info-->
      <div class="d-flex align-items-center flex-wrap mr-1">
        <!--begin::Page Heading-->
        <div class="d-flex align-items-baseline flex-wrap mr-5">
          <!--begin::Page Title-->
          <h5 class="text-dark font-weight-bold my-1 mr-5">{{ $data['subtitle'] }}</h5>
          <!--end::Page Title-->
        </div>
        <!--end::Page Heading-->
      </div>
      <!--end::Info-->
    </div>
  </div>
  <div class="container">
    {{-- Row 1: Filters --}}
    <div class="filter-group mb-4">
      <div>
        <label for="dateRange">Date Range</label>
        <input type="text" id="dateRange" class="form-control" placeholder="Select date range">
      </div>
      <div>
        <label for="platformFilter">Platform</label>
        <select id="platformFilter" class="form-select">
          <option value="">All</option>
          <option value="shopee">Shopee</option>
          <option value="tokopedia">Tokopedia</option>
        </select>
      </div>
      <div>
        <button id="filterButton">Apply Filter</button>
      </div>
    </div>

    {{-- Row 2: Table + Chart --}}
    <div class="row">
      <div class="col-md-6">
        <div class="card-3d">
          <h5 class="mb-3">Invoices</h5>
          <table class="table table-striped">
            <thead>
              <tr>
                <th>Invoice #</th>
                <th>Channel</th>
                <th>Total Items</th>
                <th>Total Price</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              @for ($i = 1; $i <= 10; $i++)
            <tr>
            <td>#INV00{{ $i }}</td>
            <td>
              @if ($i % 2 == 0)
          <span class="badge badge-shopee">Shopee</span>
          @else
          <span class="badge badge-tokopedia">Tokopedia</span>
          @endif
            </td>
            <td>
              <a href="#" class="text-primary" data-bs-toggle="modal" data-bs-target="#invoiceModal"
              onclick="loadInvoiceDetails('{{ $i }}')">
              {{ rand(1, 10) }}
              </a>
            </td>
            <td>Rp {{ number_format(rand(150000, 850000), 0, ',', '.') }}</td>
            <td>
              @if ($i % 2 == 0)
          <span class="badge bg-success">Success</span>
          @else
          <span class="badge bg-danger">Cancel</span>
          @endif
            </td>
            </tr>
        @endfor
            </tbody>
          </table>
        </div>
      </div>
      <div class="col-md-6">
        <div class="chart-card">
          <h5 class="mb-3">Cancel vs Success by Platform</h5>
          <canvas id="statusChart" height="250"></canvas>
        </div>
      </div>
    </div>

    {{-- Row 3: Tabs --}}
    <div class="row">
      <div class="col-12">
        <ul class="nav nav-tabs" id="stockTabs" role="tablist">
          <li class="nav-item">
            <button class="nav-link active" id="integrated-tab" data-bs-toggle="tab" data-bs-target="#integrated"
              type="button" role="tab">
              Integrated Stock
            </button>
          </li>
          <li class="nav-item">
            <button class="nav-link" id="tracking-tab" data-bs-toggle="tab" data-bs-target="#tracking" type="button"
              role="tab">
              Stock Tracking
            </button>
          </li>
        </ul>
        <div class="tab-content mt-3">
          <div class="tab-pane fade show active" id="integrated" role="tabpanel">
            <!-- Tabel Integrated Stock -->
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>Item Name</th>
                  <th>Variant / Size</th>
                  <th>Qty</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Adidas Ultraboost</td>
                  <td>Red / 42</td>
                  <td>120</td>
                </tr>
                <tr>
                  <td>Nike Air Max</td>
                  <td>Blue / 40</td>
                  <td>75</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="tab-pane fade" id="tracking" role="tabpanel">
            <!-- Tabel Stock Tracking -->
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>Item Name</th>
                  <th>Variant / Size</th>
                  <th>Invoice #</th>
                  <th>Qty</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Adidas Ultraboost</td>
                  <td>Red / 42</td>
                  <td>#INV001</td>
                  <td>10</td>
                  <td><span class="badge bg-success">Success</span></td>
                </tr>
                <tr>
                  <td>Nike Air Max</td>
                  <td>Blue / 40</td>
                  <td>#INV003</td>
                  <td>5</td>
                  <td><span class="badge bg-danger">Cancel</span></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@include('app._partials.js')
@include('app.v2025.omnichannel.omnichannel_js')
@include('app.v2025.omnichannel.omnichannel_modal')
@endSection()