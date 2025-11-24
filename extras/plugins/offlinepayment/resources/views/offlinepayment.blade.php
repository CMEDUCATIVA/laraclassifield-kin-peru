@php
	$post ??= [];
	$offlinepaymentPaymentMethod ??= [];
@endphp
<div class="row payment-plugin" id="offlinepaymentPayment" style="display: none;">
	<div class="col-md-10 col-sm-12 box-center center mt-4 mb-0">
		<div class="row">
			
			<div class="col-xl-12 text-center">
				<img class="img-fluid"
				     src="{{ url('plugins/offlinepayment/images/payment.png') }}"
				     title="{{ trans('offlinepayment::messages.payment_with') }}"
				     alt="{{ trans('offlinepayment::messages.payment_with') }}"
				>
			</div>
			
			<div class="col-xl-12 mt-3">
				<div id="offlinepaymentDescription">
					<div class="card card-default">
						
						<div class="card-header">
							<h3 class="panel-title">
								{{ trans('offlinepayment::messages.payment_details') }}
							</h3>
						</div>
						
						<div class="card-body">
							<h3><strong>{{ trans('offlinepayment::messages.Follow the information below to make the payment') }}:</strong></h3>
							<ul>
								<li>
									<strong>{{ trans('offlinepayment::messages.Reason for payment') }}: </strong>
									{{ trans('offlinepayment::messages.Listing') }} #{{ data_get($post, 'id') ?? 'ID' }} - <span class="package-name"></span>
								</li>
								<li>
									<strong>{{ trans('offlinepayment::messages.Amount') }}: </strong>
									<span class="amount-currency currency-in-left" style="display: none;"></span>
									<span class="payable-amount">0</span>
									<span class="amount-currency currency-in-right" style="display: none;"></span>
								</li>
							</ul>
							
							<hr class="border-0 bg-secondary">
							
							{!! data_get($offlinepaymentPaymentMethod, 'description') ?? '...' !!}
						</div>
					</div>
				</div>
			</div>
			
		</div>
    </div>
</div>

@section('after_scripts')
    @parent
    <script>
	    onDocumentReady((event) => {
		    const params = {hasForm: false, hasLocalAction: false};
			
		    loadPaymentGateway('offlinepayment', params);
        });
    </script>
@endsection
