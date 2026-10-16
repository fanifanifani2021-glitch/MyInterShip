<?php
debug_only();

$company = $orm->company();

return view('industri/subsidiary_management/index', ['company' => $company]);
