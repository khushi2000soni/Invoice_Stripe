<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
      <div class="sidebar-brand">
        <a href="{{ route('dashboard') }}">
          {{-- <div class="circleimg"><img alt="image" src="{{asset('admintheme/assets/img/shopping-bag.png') }}" class="header-logo" /></div> --}}
          <span>{{ getSetting('company_name') ?? ''}}</span>
        </a>
      </div>
      <ul class="sidebar-menu">
        <li class="menu-header">Main</li>
        <li class="{{ Request::is('dashboard*') ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}" class="nav-link">
                <x-side-bar-svg-icon icon="dashboard" />
                <span>@lang('quickadmin.qa_dashboard')</span>
            </a>
        </li>

        @can('role_access')
        <li class="{{ Request::is('roles*') ? 'active' : '' }}">
            <a href="{{route('roles.index')}}" class="nav-link">
                <x-side-bar-svg-icon icon="user" />
                <span>@lang('quickadmin.roles.title')</span></a>
        </li>
        @endcan

        @can('staff_access')
        <li class="{{ Request::is('staff*') ? 'active' : '' }}">
            <a href="{{ route('staff.index') }}" class="nav-link">
                <x-side-bar-svg-icon icon="staff" />
                <span>@lang('quickadmin.user-management.title')</span>
            </a>
        </li>
        @endcan

        @can('device_access')
        <li class="{{ Request::is('device*') ? 'active' : '' }}">
            <a href="{{ route('device.index') }}" class="nav-link">
                <x-side-bar-svg-icon icon="device" />
                <span>@lang('quickadmin.device-management.title')</span>
            </a>
        </li>
        @endcan

        @can('customer_access')
        <li class="{{ Request::is('customers*') ? 'active' : '' }}">
            <a href="{{ route('customers.index') }}" class="nav-link">
                <x-side-bar-svg-icon icon="customer" />
                <span>@lang('quickadmin.customer-management.title')</span>
            </a>
        </li>
        @endcan

        @can('invoice_access')
        <li class="{{ Request::is('orders*') ? 'active' : '' }}">
            <a href="{{ route('orders.index') }}" class="nav-link">
                <x-side-bar-svg-icon icon="invoice" />
                <span>@lang('quickadmin.order-management.title')</span>
            </a>
        </li>
        @endcan

        @can('phone_book_access')
        <li class="{{ Request::is('phone-book*') ? 'active' : '' }}">
            <a href="{{ route('showPhoneBook') }}" class="nav-link">
                <x-side-bar-svg-icon icon="phone-book" />
                <span>@lang('quickadmin.phone-book.title')</span>
            </a>
        </li>
        @endcan

        @can('report_category_access')
        <li class="{{ Request::is('reports*') ? 'active' : '' }}">
            <a href="{{ route('reports.category') }}" class="nav-link">
                <x-side-bar-svg-icon icon="report" />
                <span>@lang('quickadmin.report-management.fields.category_report')</span>
            </a>
        </li>
        @endcan

        @can('modified_menu_access')
        <li class="dropdown {{ Request::is('modified*') ? 'active' : '' }}">
            <a href="#" class="nav-link has-dropdown">
                <x-side-bar-svg-icon icon="modified" />
                <span>@lang('quickadmin.modified-management.title')</a>
            <ul class="dropdown-menu">
                @can('modified_customer_access')
                <li class="{{ Request::is('modified*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('modified.customer.index') }}">@lang('quickadmin.modified-management.fields.customer_modified')</a>
                </li>
                @endcan
                @can('modified_product_access')
                <li class="{{ Request::is('modified*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('modified.product.index') }}">@lang('quickadmin.modified-management.fields.product_modified')</a>
                </li>
                @endcan
            </ul>
        </li>
        @endcan

        @can('master_access')
        <li class="dropdown {{ Request::is('address*', 'categories*', 'products*') ? 'active' : '' }}">
            <a href="#" class="nav-link has-dropdown">
                <x-side-bar-svg-icon icon="user" />
                <span>@lang('quickadmin.master-management.title')</span></a>
            <ul class="dropdown-menu">
                @can('address_access')
                <li class="{{ Request::is('address*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('address.index') }}">@lang('quickadmin.address.title')</a>
                </li>
                @endcan
                @can('category_access')
                <li class="{{ Request::is('categories*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('categories.index') }}">@lang('quickadmin.category.title')</a>
                </li>
                @endcan
                @can('product_access')
                <li class="{{ Request::is('products*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('products.index') }}">@lang('quickadmin.product-management.title')</a>
                </li>
                @endcan
            </ul>
        </li>
        @endcan
        @can('setting_access')
        <li class="{{ Request::is('settings*') ? 'active' : '' }}">
            <a href="{{ route('settings') }}" class="nav-link">
                <x-side-bar-svg-icon icon="setting" />
                <span>@lang('quickadmin.settings.title')</span>
            </a>
        </li>
        @endcan

        @can('backup_access')
        <li class="{{ Request::is('backups*') ? 'active' : '' }}">
            <a href="{{ route('backup.index') }}" class="nav-link">
                <x-side-bar-svg-icon icon="backup" />
                <span>@lang('quickadmin.backup.title')</span>
            </a>
        </li>
        @endcan

        <li class="{{ Request::is('logout*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('logout') }}">
                <x-side-bar-svg-icon icon="logout" />
                <span>@lang('quickadmin.qa_logout')</span>
            </a>
        </li>
    </ul>

    </aside>
  </div>
