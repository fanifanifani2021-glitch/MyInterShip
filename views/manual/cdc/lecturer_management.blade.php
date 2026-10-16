@extends('templates.manual')

@section('manual-title')
Lecturer Management Manual
@endsection

@section('manual-subtitle')
Guide for managing lecturer data, imports, and account roles
@endsection

@section('manual-toc')
<li><a href="{{ Router::currentUrl() }}#overview">Overview</a></li>
<li><a href="{{ Router::currentUrl() }}#concept">Concept & Validation</a></li>
<li><a href="{{ Router::currentUrl() }}#usage">Step-by-step Usage</a></li>
<li><a href="{{ Router::currentUrl() }}#import">Data Import</a></li>
<li><a href="{{ Router::currentUrl() }}#notes">Important Notes</a></li>
@endsection

@section('manual-content')

{{-- ================= OVERVIEW ================= --}}
<div class="manual-section" id="overview">
    <h4>📌 Overview</h4>
    <p>
        The Lecturer Management module is a centralized database for managing
        all lecturer data within the internship ecosystem. This includes
        lecturers from <b>Polibatam</b> as well as <b>Partner Universities</b>.
    </p>

    <p>
        Registered lecturers can be assigned various roles, such as
        <b>Internship Supervisors</b> (Dosen Pembimbing) or
        <b>Study Program Coordinators</b> (Koordinator Prodi).
    </p>

    <p>
        This module supports single-entry data management as well as
        <b>bulk data import</b> via Excel files to facilitate large-scale
        data processing at the beginning of the semester.
    </p>
</div>

{{-- ================= CONCEPT ================= --}}
<div class="manual-section" id="concept">
    <h4><i class="fa fa-user-tie"></i> Concept & Validation</h4>

    <div class="step-box">
        <b>1. Unique Identification</b><br>
        Every lecturer must have a unique identifier. The system prioritizes
        <b>NIK</b> (Nomor Induk Karyawan).
        Duplicate IDs are strictly prohibited to ensure data integrity.
    </div>

    <div class="step-box">
        <b>2. Email & Account Association</b><br>
        The email address provided is crucial as it serves as the
        <b>login credential</b> for the lecturer.
        <br><br>
        Ensure the email is valid and active. The system prevents
        registering multiple lecturers with the same email address.
    </div>

    <div class="step-box">
        <b>3. Data Safety (Soft Delete)</b><br>
        To prevent accidental data loss, deleting a lecturer performs a
        <b>Soft Delete</b>. The data is moved to the Recycle Bin and
        can be restored later.
        <br><br>
        <b>Permanent Deletion</b> is only available within the Recycle Bin menu.
    </div>
</div>

{{-- ================= USAGE ================= --}}
<div class="manual-section" id="usage">
    <h4><i class="fa fa-list-ol"></i> Step-by-step Usage</h4>

    <div class="step-box">
        <b>1. Accessing the Menu</b><br>
        Navigate to <b>Academic → Lecturer Management</b> on the sidebar.
        You will see the main table listing all active lecturers.
        <br><br>
        <figure class="manual-figure">
            <img 
                src="" 
                class="manual-img" 
                alt="Lecturer Management - Main View"
            >
            <figcaption class="manual-caption">
                Figure 1. Lecturer Management Main View
            </figcaption>
        </figure>
    </div>

    <div class="step-box">
        <b>2. Adding a Lecturer Manually</b><br>
        Click the <b>Add Data</b> button to open the registration form.
        Fill in the required details:
        <ul>
            <li><b>Name:</b> Full name with academic titles.</li>
            <li><b>ID Number:</b> NIP/NIDN/NIK.</li>
            <li><b>Email & Phone:</b> Contact details.</li>
        </ul>
        <br>
        <figure class="manual-figure">
            <img 
                src="" 
                class="manual-img" 
                alt="Lecturer Management - Add Form"
            >
            <figcaption class="manual-caption">
                Figure 2. Manual Lecturer Registration Form
            </figcaption>
        </figure>
    </div>

    <div class="step-box">
        <b>3. Managing Selections (Bulk Action)</b><br>
        You can select multiple lecturers by clicking the checkboxes
        on the left side of the table.
        <br><br>
        Once selected, a <b>Bulk Action Toolbar</b> will appear at the top,
        allowing you to <b>Delete</b> multiple records at once or
        <b>View</b> the selected list.
        <br><br>
        <figure class="manual-figure">
            <img 
                src="" 
                class="manual-img" 
                alt="Lecturer Management - Bulk Action"
            >
            <figcaption class="manual-caption">
                Figure 3. Selecting multiple lecturers for bulk deletion
            </figcaption>
        </figure>
    </div>

    <div class="step-box">
        <b>4. Recycle Bin (Restoring Data)</b><br>
        If you accidentally delete a lecturer, click the <b>Recycle</b> button.
        <br><br>
        In this menu, you can find all deleted records.
        Click <b>Restore</b> to bring them back to the active list,
        or <b>Delete Permanently</b> to remove them forever.
        <br><br>
        <figure class="manual-figure">
            <img 
                src="" 
                class="manual-img" 
                alt="Lecturer Management - Recycle Bin"
            >
            <figcaption class="manual-caption">
                Figure 4. Restoring data from Recycle Bin
            </figcaption>
        </figure>
    </div>
</div>

{{-- ================= IMPORT ================= --}}
<div class="manual-section" id="import">
    <h4><i class="fa fa-file-excel"></i> Data Import (Excel)</h4>

    <div class="step-box">
        <b>1. Download Template</b><br>
        Click the <b>Import</b> button. In the modal/page that appears,
        download the provided <b>Excel Template</b>.
        <br><br>
        Do not alter the header structure of the template to ensure
        successful data parsing.
    </div>

    <div class="step-box">
        <b>2. Upload & Preview</b><br>
        Upload your filled Excel file. The system will process the file
        and show a <b>Preview Table</b>.
        <br><br>
        <figure class="manual-figure">
            <img 
                src="" 
                class="manual-img" 
                alt="Lecturer Management - Import Preview"
            >
            <figcaption class="manual-caption">
                Figure 5. Import Preview showing valid and invalid data
            </figcaption>
        </figure>
        <br>
        Rows with errors (e.g., duplicate NIP, missing email) will be flagged.
        Valid rows will be ready for import.
    </div>

    <div class="step-box">
        <b>3. Execute Import</b><br>
        Click <b>Process Import</b>. The system will insert the valid data
        into the database. This process runs in the background to handle
        large datasets efficiently.
    </div>
</div>

{{-- ================= NOTES ================= --}}
<div class="manual-section" id="notes">
    <h4><i class="fa fa-exclamation-triangle"></i> Important Notes</h4>

    <div class="manual-note">
        <b>Foreign Key Constraints:</b> You cannot permanently delete a lecturer
        if they are currently assigned as a supervisor to an active student.
        You must unassign them first.
    </div>

    <div class="manual-note">
        <b>Email Changes:</b> Changing a lecturer's email address will
        automatically update their login username. Ensure the lecturer
        is notified of this change.
    </div>

    <div class="manual-note">
        <b>Large Imports:</b> For imports larger than 500 rows,
        please allow some time for the system to process the data.
        Do not close the browser tab until the success message appears.
    </div>
</div>

@endsection