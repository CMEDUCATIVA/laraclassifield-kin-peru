@if (!empty($categories))
	@foreach($categories as $key => $cat)
		<div class="col-lg-2 col-md-3 col-sm-4 col-6 f-category">
			<a href="{{ \App\Services\UrlGen::category($cat) }}">
				<img src="{{ data_get($cat, 'image_url') }}" class="lazyload img-fluid" alt="{{ data_get($cat, 'name') }}">
				<h6>
					{{ data_get($cat, 'name') }}
					@if (config('settings.listings_list.count_categories_listings'))
						&nbsp;({{ $countPostsPerCat[data_get($cat, 'id')]['total'] ?? 0 }})
					@endif
				</h6>
			</a>
		</div>
	@endforeach
@endif
