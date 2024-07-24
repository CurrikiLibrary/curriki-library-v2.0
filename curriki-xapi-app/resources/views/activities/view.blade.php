@extends('layouts.activity_layout')
@section('content')
	<div class="jumbotron px-0">
		<div class="container">
			<div class="data-box">
				<div class="data-head">
					<div class="row justify-content-md-center">
						<div class="col-md-11">
							<div class="d-flex justify-content-between align-items-center mx-auto">
								<div class="head-left">
									<h4 class="font-size-18 d-inline-block align-middle">{{$activity->title}}</h4>
								</div>
								<div class="dropdown">
									<button class="btn btn-default" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										<i class="fa fa-ellipsis-v"></i>
									</button>
									<div class="dropdown-menu dropdown-menu-right">
										<a class="dropdown-item" href="#"><i class="fa fa-question-circle" aria-hidden="true"></i> Help</a>
										<a class="dropdown-item" href="#"><i class="fa fa-share-alt" aria-hidden="true"></i> Share</a>
										<a class="dropdown-item" href="#"><i class="fa fa-flag" aria-hidden="true"></i> Report</a>
										<a class="dropdown-item" href="{{url('/lessons/'.$activity->lesson->id)}}"><i class="fa fa-sign-out" aria-hidden="true"></i> Exit</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="data-body-x" style="min-height:60em; padding:10px">
					{!! $lti_content !!}
				</div>
			</div>
			<div class="row pt-4">
				<div class="col-md-12">
					@if($activity->previous_activity() != null)
						<a class="btn btn-blue pull-left" href="{{url('/activities/'.$activity->previous_activity()->id)}}">
							Previous activity
						</a>
					@endif
					@if($activity->next_activity() != null)
						<a class="btn btn-blue pull-right" href="{{url('/activities/'.$activity->next_activity()->id)}}">
							Next activity
						</a>
					@endif
				</div>
			</div>
		</div>
	</div>
@endsection
@section('styles')
@endsection
@section('scripts')
@endsection