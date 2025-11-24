@php
	$catDisplayType ??= 'cc_normal_list';
	$styled = ($catDisplayType == 'cc_normal_list_s') ? ' styled' : '';
@endphp

<div style="clear: both;"></div>

@if (!empty($categories))
	<div class="col-xl-12">
		<div class="list-categories-children{{ $styled }}">
			<div class="row px-3">
				@foreach ($categories as $key => $cols)
					<div class="col-md-4 col-sm-4 {{ (count($categories) == $key+1) ? 'last-column' : '' }}">
						@foreach ($cols as $iCat)
							@php
								$randomId = '-' . substr(uniqid(rand(), true), 5, 5);
							@endphp
							<div class="cat-list">
								<h3 class="cat-title rounded">
									@if (in_array(config('settings.listings_list.show_category_icon'), [2, 6, 7, 8]))
										<i class="{{ data_get($iCat, 'icon_class') ?? 'fa-solid fa-check' }}"></i>&nbsp;
									@endif
									<a href="{{ \App\Services\UrlGen::category($iCat) }}">
										{{ data_get($iCat, 'name') }}
										@if (config('settings.listings_list.count_categories_listings'))
											&nbsp;({{ $countPostsPerCat[data_get($iCat, 'id')]['total'] ?? 0 }})
										@endif
									</a>
									<span class="btn-cat-collapsed collapsed"
									      data-bs-toggle="collapse"
									      data-bs-target=".cat-id-{{ data_get($iCat, 'id') . $randomId }}"
									      aria-expanded="false"
									>
										<span class="icon-down-open-big"></span>
									</span>
								</h3>
								<ul class="cat-collapse collapse show cat-id-{{ data_get($iCat, 'id') . $randomId }} long-list-home">
									@if (isset($subCategories[data_get($iCat, 'id')]))
										@php
											$catSubCats = $subCategories[data_get($iCat, 'id')];
										@endphp
										@foreach ($catSubCats as $iSubCat)
											<li>
												<a href="{{ \App\Services\UrlGen::category($iSubCat) }}">
													{{ data_get($iSubCat, 'name') }}
												</a>
												@if (config('settings.listings_list.count_categories_listings'))
													&nbsp;({{ $countPostsPerCat[data_get($iSubCat, 'id')]['total'] ?? 0 }})
												@endif
											</li>
										@endforeach
									@endif
								</ul>
							</div>
						@endforeach
					</div>
				@endforeach
			</div>
		</div>
		
		<div style="clear: both;"></div>
	</div>
@endif
