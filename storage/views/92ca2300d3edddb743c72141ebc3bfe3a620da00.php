

<?php $__env->startSection('manual-title'); ?>
Attendance Location Manual
<?php $__env->stopSection(); ?>

<?php $__env->startSection('manual-subtitle'); ?>
Guide for managing attendance location and validation rules
<?php $__env->stopSection(); ?>

<?php $__env->startSection('manual-toc'); ?>
<li><a href="<?php echo e(Router::currentUrl()); ?>#overview">Overview</a></li>
<li><a href="<?php echo e(Router::currentUrl()); ?>#concept">Concept & Validation</a></li>
<li><a href="<?php echo e(Router::currentUrl()); ?>#usage">Step-by-step Usage</a></li>
<li><a href="<?php echo e(Router::currentUrl()); ?>#datatable">Data Management</a></li>
<li><a href="<?php echo e(Router::currentUrl()); ?>#notes">Important Notes</a></li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('manual-content'); ?>



<div class="manual-section" id="overview">
    <h4>📌 Overview</h4>
    <p>
        Attendance Location is a location-based validation feature
        designed to control internship attendance check-ins.
        Each company can define one or more allowed attendance locations
        by specifying a geographic center point and a maximum radius.
    </p>

    <p>
        During the check-in process, the system compares the intern's
        real-time GPS coordinates with the registered attendance locations.
        A check-in will only be accepted if the intern is physically
        located within the permitted radius.
    </p>

    <p>
        This feature helps ensure attendance integrity, prevents
        off-site or unauthorized check-ins, and supports accurate
        monitoring of internship activities.
    </p>

</div>

<div class="manual-section" id="concept">
    <h4><i class="fa fa-compass"></i> Concept & Validation</h4>

    <div class="step-box">
        <b>1. Add Attendance Location</b><br>
        Create a new attendance location by providing a clear
        <b>location name</b> (e.g. Office, Factory, Project Site).
        This name helps identify the location in the system.
        <br><br>
        Next, select the <b>location point directly from the map</b>
        The system will automatically capture the latitude and longitude
        based on the selected point.
        <br><br>
        Finally, define the <b>allowed radius (in meters)</b> to determine
        the maximum distance an intern can be from the location center
        when checking in.
    </div>


    <div class="step-box">
        <b>2. Intern Check-in Validation</b><br>
        When an intern performs a check-in, the system automatically
        captures the GPS location from the device.
        <br><br>
        The captured location is then compared with all registered
        attendance locations for the company.
        Check-in is only allowed if the intern is located
        <b>inside the defined radius</b> of at least one attendance location.
        <br><br>
        If the intern is outside the allowed area or the GPS accuracy
        is too low, the check-in process will be rejected.
    </div>

    <div class="step-box">
        <b>3. Update Attendance Location</b><br>
        Administrators can update the location name, map position,
        or radius at any time when needed.
        <br><br>
        Any changes made to the master attendance location will
        <b>only apply to future check-ins</b>.
        Existing attendance records will remain unchanged
        to preserve historical accuracy.
    </div>

</div>



<div class="manual-section" id="usage">
    <h4><i class="fa fa-list-ol"></i> Step-by-step Usage</h4>

    <div class="step-box">
        <b>1. Open Attendance Location Menu</b><br>
        Navigate to <b>Internship Preparation → Attendance Location</b>.
        The system will display a list of registered locations
        using a server-side DataTable.
        <br><br>
        <figure class="manual-figure">
            <img
                src="<?php echo e(router('manual.attendance_location', ['step' => '01'])); ?>"
                class="manual-img"
                alt="Attendance Location - Add Location"
            >
            <figcaption class="manual-caption">
                Figure 1. Attendance Location Menu on Sidebar
            </figcaption>
        </figure>

    </div>

    <div class="step-box">
        <b>2. Add New Attendance Location</b><br>
        Click the <b>Add Location</b> button.
        A modal form with an interactive map will appear.
        <br><br>

        <figure class="manual-figure">
            <img
                src="<?php echo e(router('manual.attendance_location', ['step' => '02'])); ?>"
                class="manual-img"
                alt="Attendance Location - Add Location Button"
            >
            <figcaption class="manual-caption">
                Figure 2. Attendance Location - Add Location Button
            </figcaption>
        </figure>

        <figure class="manual-figure">
            <img
                src="<?php echo e(router('manual.attendance_location', ['step' => '03'])); ?>"
                class="manual-img"
                alt="Attendance Location - Add Location Button"
            >
            <figcaption class="manual-caption">
                Figure 3. Attendance Location - Add Location Modal
            </figcaption>
        </figure>

        Fill in the following fields:
        <ul>
            <li><b>Location Name</b> – Example: “Main Office”</li>
            <li><b>Map Point</b> – Click on map or drag the marker</li>
            <li><b>Radius</b> – Allowed distance in meters</li>
        </ul>

        
        <br><br>
        You may also use the <b>Use Current Location</b> button
        to automatically detect your current GPS position.
        Please ensure that you grant the browser permission
        to access your device’s location data.
        <br><br>


        <figure class="manual-figure">
            <img
                src="<?php echo e(router('manual.attendance_location', ['step' => '04'])); ?>"
                class="manual-img"
                alt="Attendance Location - Add Location Button"
            >
            <figcaption class="manual-caption">
                Figure 3. Attendance Location - Add Location Modal
            </figcaption>
        </figure>


        <br><br>
        You may change the <b>attendance radius</b> as needed.
        By default, the radius is set to <b>100 meters</b>.
        This radius defines the maximum distance allowed
        between the intern’s actual location and the registered
        attendance location during check-in.
        <br><br>
        <b>Important considerations:</b>
        <ul>
            <li>
                <b>Increasing the radius</b> allows check-ins from a wider area,
                which may be useful for large campuses, factories, or outdoor
                work sites. However, a larger radius may reduce location accuracy
                and increase the risk of off-site check-ins.
            </li>
            <li>
                <b>Decreasing the radius</b> enforces stricter location validation
                and improves attendance accuracy. This is recommended for offices
                or buildings with a clearly defined check-in area, but may cause
                check-in failures if GPS accuracy is unstable.
            </li>
        </ul>
        <br>


        
        <figure class="manual-figure">
            <img
                src="<?php echo e(router('manual.attendance_location', ['step' => '05'])); ?>"
                class="manual-img"
                alt="Attendance Location - Smaller Radius Area"
            >
            <figcaption class="manual-caption">
                Figure 5. Attendance Location with a smaller radius (stricter check-in area)
            </figcaption>
        </figure>

        
        <figure class="manual-figure">
            <img
                src="<?php echo e(router('manual.attendance_location', ['step' => '06'])); ?>"
                class="manual-img"
                alt="Attendance Location - Bigger Radius Area"
            >
            <figcaption class="manual-caption">
                Figure 6. Attendance Location with a larger radius (wider check-in area)
            </figcaption>
        </figure>

        
    </div>

    <div class="step-box">
        <b>3. Save Location</b><br>
        Click the <b>Save Location</b> button at the bottom of the modal
        to submit the attendance location data.
        <br><br>
        The data will be saved using AJAX without reloading the page.
        If the process is successful, the attendance location table
        will refresh automatically to display the latest data.
    </div>

    <div class="step-box">
        <b>4. Edit Attendance Location</b><br>
        Click the <b>Edit</b> button on the selected attendance location row.
        The same modal form will open and automatically load
        the existing location data.
        <br><br>
        You may update the <b>location name</b>, adjust the
        <b>map position</b> by dragging the marker or clicking on the map,
        and modify the <b>attendance radius</b> as needed.
        <br><br>
        After saving, the updated data will be applied
        <b>only to future check-ins</b>.
        Existing attendance records will remain unchanged
        to preserve historical accuracy.
    </div>


    <div class="step-box">
        <b>5. Delete Attendance Location</b><br>
        Click the <b>Delete</b> button on the selected attendance location row.
        A confirmation dialog will appear to prevent accidental deletion.
        <br><br>
        When confirmed, the attendance location will be
        <b>soft deleted</b>.
        This means the data is not permanently removed from the system
        and will be moved to the <b>Recycle</b> menu.
        <br><br>
        Soft-deleted locations will no longer be available
        for new attendance check-ins,
        but they can still be <b>restored</b> if needed.
    </div>


    <div class="step-box">
        <b>6. Permanent Delete (Recycle Menu)</b><br>
        Open the <b>Recycle</b> menu from the Attendance Location page
        to view all soft-deleted locations.
        <br><br>
        From this menu, you may choose to
        <b>restore</b> a deleted location
        or perform a <b>permanent delete</b>.
        <br><br>
        Permanent deletion will completely remove the data
        from the system and <b>cannot be undone</b>.
        Make sure the location is no longer needed
        before performing this action.
    </div>



</div>


<div class="manual-section" id="datatable">
    <h4><i class="fa fa-table"></i> Data Management</h4>

    <div class="step-box">
        <b>Server-side DataTable</b><br>
        Attendance locations are displayed using
        <b>server-side DataTables</b> with AJAX.
        This ensures optimal performance even with large datasets.
    </div>

    <div class="step-box">
        <b>Delete (Soft Delete)</b><br>
        Clicking the <b>Delete</b> button will not permanently
        remove the data.
        <br><br>
        The location will be moved to the <b>Recycle</b> menu
        and can be restored later if needed.
    </div>
</div>


<div class="manual-section" id="notes">
    <h4><i class="fa fa-exclamation-triangle"></i> Important Notes</h4>

    <div class="manual-note">
        Each attendance record stores the <b>nearest attendance location</b>
        at the time of check-in, including coordinates and radius.
    </div>

    <div class="manual-note">
        Attendance history is <b>immutable</b>.
        Updates to master location data will not affect past records.
    </div>

    <div class="manual-note">
        GPS accuracy affects validation results.
        Low accuracy may cause check-in rejection.
    </div>

    <div class="manual-note">
        Use of fake GPS or manipulated location data
        may result in failed validation and system flags.
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('templates.manual', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\myinternship\views/manual/industry/attendance_location.blade.php ENDPATH**/ ?>