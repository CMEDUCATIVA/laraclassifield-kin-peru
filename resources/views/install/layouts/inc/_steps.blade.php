@php
	$navLinks ??= [];
	$step ??= 0;
	$current ??= 1;
@endphp
<ul class="nav nav-pills justify-content-center install-steps">
	@forelse($navLinks as $link)
		@php
			$linkStep = (int)data_get($link, 'step');
			$isUnlockedLink = (bool)data_get($link, 'unlocked');
			$linkPrevStep = $linkStep - 1;
			
			$enabledClass = ($step >= $linkPrevStep) ? ' enabled' : '';
			$activeClass = ($current == $linkStep) ? ' active' : '';
			$disabledClass = (!$isUnlockedLink) ? ' disabled' : '';
		@endphp
		<li class="nav-item{{ $enabledClass }}">
			<a class="nav-link{{ $disabledClass.$activeClass }}" href="{{ data_get($link, 'url') }}">
				<i class="{{ data_get($link, 'icon') }}"></i> {{ data_get($link, 'label') }}
			</a>
		</li>
	@empty
		<div class="col-xl-12">
			<div class="alert alert-danger">
				The navigation bar could not be loaded.
				Please report this issue to us <a href="https://support.laraclassifier.com/hc/tickets/new" target="_blank">here</a>.
			</div>
		</div>
	@endforelse
</ul>
