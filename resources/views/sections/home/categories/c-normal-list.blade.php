@php
	$catDisplayType ??= 'c_normal_list';
	
	$listTab = [
		'c_border_list' => 'list-border',
	];
	$catListClass = (isset($listTab[$catDisplayType])) ? 'list ' . $listTab[$catDisplayType] : 'list';
@endphp
@if (!empty($categories))
	<div class="col-xl-12">
		<div class="list-categories">
			<div class="row">
				@foreach ($categories as $key => $items)
					<ul class="cat-list {{ $catListClass }} col-md-4 {{ (count($categories) == $key+1) ? 'cat-list-border' : '' }}">
						@foreach ($items as $k => $cat)
							<li>
								@if (in_array(config('settings.listings_list.show_category_icon'), [2, 6, 7, 8]))
									<i class="{{ data_get($cat, 'icon_class') ?? 'fa-solid fa-check' }}"></i>&nbsp;
								@endif
								<a href="{{ \App\Services\UrlGen::category($cat) }}">
									{{ data_get($cat, 'name') }}
								</a>
								@if (config('settings.listings_list.count_categories_listings'))
									&nbsp;({{ $countPostsPerCat[data_get($cat, 'id')]['total'] ?? 0 }})
								@endif
							</li>
						@endforeach
					</ul>
				@endforeach
			</div>
		</div>
	</div>
@endif
