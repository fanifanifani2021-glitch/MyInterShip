@extends('templates.cdc')

@push('head-scripts')
@php
$id_kampus = $_SESSION['CDC_Data'] ['id_kampus'];
$query = $_SERVER['PDO']->prepare("SELECT * FROM tb_kampus WHERE id_kampus = :id_kampus LIMIT 1");
$query->execute( array('id_kampus' => $id_kampus) );
$university_data = $query->fetch( PDO::FETCH_ASSOC );
@endphp

@endpush

@section('main-content')


<div class="container">



    <div class="panel-header bg-primary-gradient">
        <div class="page-inner py-5">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                <div>
                    <h2 class="text-white pb-2 fw-bold">My University</h2>
                    <h5 class="text-white op-7 mb-2">University Identity Detail</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="page-inner mt--5">
        <div class="row mt--2">


            <div class="col-md-12">
                <div class="card card-profile">

                    <div class="card-header"
                        style="background-image: url('index.php?viewer=profile_background'); height: 300px; background-size: cover;background-repeat: no-repeat;">


                        <!-- <a href="#" data-toggle="modal" data-target="#ModalBackground" type="button" role="button " class="btn btn-primary btn-sm pull-right"> <i class="fas fa-edit mr-2"></i>Change Background</a> -->

                        <div class="profile-picture">
                            <div class="avatar avatar-xxl">
                                <img src="index.php?viewer=get_university_logo&location=<?= base64_encode($university_data['logo'] ?? '--') ?>"
                                    alt="..." class="avatar-img rounded-circle">
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="user-profile text-center mb-4">

                            <div class="name"><?= $university_data['nama_kampus'] ?></div>
                            <div class="job">Joined My Internship since
                                <b><?= ($university_data['id_kampus'] == 1) ? 'Beginning' : date( 'd M Y',  strtotime($university_data['created_at']) ) ?></b>
                            </div>

                        </div>




                        <table class="table table-bordered table-hover table-responsive-md">
                            <tbody>
                                <tr>
                                    <th width="30%">Name <span class="float-right">:</span></th>
                                    <td width="70%"><span><?= $university_data['nama_kampus']; ?></span></td>
                                </tr>

                                <tr>
                                    <th width="30%">Logo <span class="float-right">:</span></th>
                                    <td width="70%"><span><?= $university_data['logo']; ?></span></td>
                                </tr>

                                <tr>
                                    <th width="30%">Website <span class="float-right">:</span></th>
                                    <td width="70%"><span><?= $university_data['website']; ?></span></td>
                                </tr>

                            </tbody>
                        </table>


                        <button class="btn btn-primary btn-sm float-left" data-toggle="modal"
                            data-target="#ModalEdit"><i class="fas fa-edit mr-1"></i>Edit</button>


                    </div>


                </div>
            </div>





        </div>


        <div class="modal fade" id="ModalBackground">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <span class="fw-mediumbold">
                                Change Background</span>
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>


                </div>
            </div>
            <!-- /.modal-content -->
        </div>


        <!-- modal edit -->
        <div class="modal fade" id="ModalEdit">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <span class="fw-mediumbold">
                                Edit University Data</span>
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <form action="{{ router('cdc/my/university.post') }}" method="POST"> <?= csrf(); ?>

                        <div class="modal-body form-row">


                            <div class="form-group col-md-12">
                                <label for="name">Name <label class="text-danger">*</label></label>
                                <input type="text" placeholder="Fill University Name"
                                    class="form-control <?= show_error('name') ?>" name="name" id="name2"
                                    value="<?= old('name', $university_data['nama_kampus']) ?>">
                                <?= form_error('name') ?>
                            </div>


                            <div class="form-group col-md-12">
                                <label for="header">Logo <label class="text-danger">*</label></label>
                                <input type="text" placeholder="Provile Link to Company Logo"
                                    class="form-control <?= show_error('logo') ?>" name="logo" id="logo2"
                                    value="<?= $university_data['logo']; ?>">
                                <?= form_error('logo'); ?>
                            </div>

                            <div class="form-group col-md-12">
                                <label for="website">Website</label>
                                <input type="text" placeholder="Fill Company Website"
                                    class="form-control <?= show_error('website') ?>" name="website" id="website2"
                                    value="<?= $university_data['website']; ?>">
                                <?= form_error('website') ?>

                            </div>



                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i
                                    class="fas fa-times mr-1"></i>Close</button>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-edit mr-1"></i>Edit</button>
                    </form>

                </div>
            </div>
            <!-- /.modal-content -->
        </div>


    </div>


</div>





@endsection

@push('footer-scripts')
@if (old('ModalEdit') !== NULL) :
<script>
    $(document).ready(function () {
        $('#ModalEdit').modal('show');
    });
</script>
@endif
@endpush