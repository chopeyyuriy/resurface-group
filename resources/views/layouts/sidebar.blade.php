<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">
    <div data-simplebar class="h-100">
        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <!-- Side menu -->
                <li>
                    <a href="{{ route('root') }}" class="waves-effect">
                        <i class="bx bx-home-alt"></i>
                        <span key="t-dashboard">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('erm') }}" class="waves-effect">
                        <i class="bx bx-briefcase-alt"></i>
                        <span key="t-erm">EMR</span>
                    </a>
                </li>
                @hasrole('admin')
                <li>
                    <a href="{{ route('documents') }}" class="waves-effect">
                        <i class="bx bx-file"></i>
                        <span key="t-documents">Documents</span>
                    </a>
                </li>
                @endhasrole
                <li>
                    <a href="{{ route('messaging') }}" class="waves-effect">
                        <i class="bx bx-message-dots"></i>
                        <span key="t-messaging">Messaging</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('calendar') }}" class="waves-effect">
                        <i class="bx bx-calendar"></i>
                        <span key="t-calendar">Events Calendar</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('time_reporting') }}" class="waves-effect">
                        <i class="bx bx-timer"></i>
                        <span key="t-time-reporting">Time Reporting</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('clinician_directory') }}" class="waves-effect">
                        <i class="bx bxs-user-detail"></i>
                        <span key="t-clinician-directory">Clinician Directory</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('faq') }}" class="waves-effect">
                        <i class="bx bx-help-circle"></i>
                        <span key="t-support">FAQ / Support</span>
                    </a>
                </li>
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
<!-- Left Sidebar End -->
