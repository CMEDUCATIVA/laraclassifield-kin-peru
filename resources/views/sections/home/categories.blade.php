@php
	$sectionOptions = $categoriesOptions ?? [];
	$sectionData ??= [];
	$categories = (array)data_get($sectionData, 'categories');
	$subCategories = (array)data_get($sectionData, 'subCategories');
	$countPostsPerCat = (array)data_get($sectionData, 'countPostsPerCat');
	$countPostsPerCat = collect($countPostsPerCat)->keyBy('id')->toArray();
	
	$hideOnMobile = (data_get($sectionOptions, 'hide_on_mobile') == '1') ? ' hidden-sm' : '';
	
	$catDisplayType = data_get($sectionOptions, 'cat_display_type');
	$maxSubCats = (int)data_get($sectionOptions, 'max_sub_cats');
@endphp

@includeFirst([
	config('larapen.core.customizedViewPath') . 'sections.spacer',
	'sections.spacer'
], ['hideOnMobile' => $hideOnMobile])

<div class="container{{ $hideOnMobile }}">
	<div class="col-xl-12 content-box layout-section">
		<div class="row row-featured row-featured-category">
			<div class="col-xl-12 box-title no-border">
				<div class="inner">
					<h2>
						<span class="title-3">
							{{ t('Browse by') }} <span class="fw-bold">{{ t('category') }}</span>
						</span>
						<a href="{{ \App\Services\UrlGen::sitemap() }}" class="sell-your-item">
							{{ t('View more') }} <i class="fa-solid fa-bars"></i>
						</a>
					</h2>
				</div>
			</div>
			
			@if ($catDisplayType == 'c_picture_list')
				
				@includeFirst([
					config('larapen.core.customizedViewPath') . 'sections.home.categories.c-picture-list',
					'sections.home.categories.c-picture-list'
				])
				
			@elseif ($catDisplayType == 'c_bigIcon_list')
				
				@includeFirst([
					config('larapen.core.customizedViewPath') . 'sections.home.categories.c-big-icon-list',
					'sections.home.categories.c-big-icon-list'
				])
				
			@elseif (in_array($catDisplayType, ['cc_normal_list', 'cc_normal_list_s']))
				
				@includeFirst([
					config('larapen.core.customizedViewPath') . 'sections.home.categories.cc-normal-list',
					'sections.home.categories.cc-normal-list'
				])
			
			@elseif (in_array($catDisplayType, ['c_normal_list', 'c_border_list']))
				
				@includeFirst([
					config('larapen.core.customizedViewPath') . 'sections.home.categories.c-normal-list',
					'sections.home.categories.c-normal-list'
				])
				
			@else
				
				{{-- Called only when issue occurred --}}
				@includeFirst([
					config('larapen.core.customizedViewPath') . 'sections.home.categories.c-big-icon-list',
					'sections.home.categories.c-big-icon-list'
				])
				
			@endif
			
		</div>
	</div>
</div>

@section('before_scripts')
	@parent
	@if ($maxSubCats >= 0)
		<script>
			var maxSubCats = {{ $maxSubCats }};
		</script>
	@endif
@endsection
