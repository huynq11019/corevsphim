<?php

namespace App\Library;

use Ophim\Crawler\OphimCrawler\Option as BaseOption;
use Backpack\Settings\app\Models\Setting;
use Illuminate\Support\Facades\Cache;

class CustomOption extends BaseOption
{
    public static function getAllOptions()
    {
        $categories = [];
        $regions = [];
        try {
            $categories = Cache::remember('ophim_categories', 86400, function () {
                $data = json_decode(file_get_contents(sprintf('%s/the-loai', config('ophim_crawler.domain', 'https://ophim1.com'))), true) ?? [];
                return collect($data)->pluck('name', 'name')->toArray();
            });

            $regions = Cache::remember('ophim_regions', 86400, function () {
                $data = json_decode(file_get_contents(sprintf('%s/quoc-gia', config('ophim_crawler.domain', 'https://ophim1.com'))), true) ?? [];
                return collect($data)->pluck('name', 'name')->toArray();
            });
        } catch (\Throwable $th) {

        }

        $fields = [
            'episodes' => 'Tập mới',
            'status' => 'Trạng thái phim',
            'episode_time' => 'Thời lượng tập phim',
            'episode_current' => 'Số tập phim hiện tại',
            'episode_total' => 'Tổng số tập phim',
            'name' => 'Tên phim',
            'origin_name' => 'Tên gốc phim',
            'content' => 'Mô tả nội dung phim',
            'thumb_url' => 'Ảnh Thumb',
            'poster_url' => 'Ảnh Poster',
            'trailer_url' => 'Trailer URL',
            'quality' => 'Chất lượng phim',
            'language' => 'Ngôn ngữ',
            'notify' => 'Nội dung thông báo',
            'showtimes' => 'Giờ chiếu phim',
            'publish_year' => 'Năm xuất bản',
            'is_copyright' => 'Đánh dấu có bản quyền',
            'type' => 'Định dạng phim',
            'is_shown_in_theater' => 'Đánh dấu phim chiếu rạp',
            'actors' => 'Diễn viên',
            'directors' => 'Đạo diễn',
            'categories' => 'Thể loại',
            'regions' => 'Khu vực',
            'tags' => 'Từ khóa',
            'studios' => 'Studio',
        ];

        $options = [
            'domain' => [
                'name' => 'domain',
                'label' => 'API Domain',
                'type' => 'text',
                'value' => 'https://ophim1.com',
                'tab' => 'Setting'
            ],
            'domain_nguonc' => [
                'name' => 'domain_nguonc',
                'label' => 'Nguonc API Domain',
                'type' => 'text',
                'value' => 'https://phim.nguonc.com',
                'tab' => 'Setting'
            ],
            'domain_kkphim' => [
                'name' => 'domain_kkphim',
                'label' => 'KKPhim API Domain',
                'type' => 'text',
                'value' => 'https://phimapi.com',
                'tab' => 'Setting'
            ],
               'domain_xxvnapi' => [
                'name' => 'domain_xxvnapi',
                'label' => 'xxvnapi API Domain',
                'type' => 'text',
                'value' => 'https://xxvnapi.com',
                'tab' => 'Setting'
            ],
            'crawler_ophim_enable' => [
                'name' => 'crawler_ophim_enable',
                'label' => 'Bật crawler Ophim',
                'type' => 'checkbox',
                'tab' => 'Setting'
            ],
            'crawler_nguonc_enable' => [
                'name' => 'crawler_nguonc_enable',
                'label' => 'Bật crawler Nguonc',
                'type' => 'checkbox',
                'tab' => 'Setting'
            ],
            'crawler_kkphim_enable' => [
                'name' => 'crawler_kkphim_enable',
                'label' => 'Bật crawler KKPhim',
                'type' => 'checkbox',
                'tab' => 'Setting'
            ],
            'crawler_xxvnapi_enable' => [
                'name' => 'crawler_xxvnapi_enable',
                'label' => 'Bật crawler xxvnapi',
                'type' => 'checkbox',
                'tab' => 'Setting'
            ],
            'download_image' => [
                'name' => 'download_image',
                'label' => 'Tải ảnh khi crawl',
                'type' => 'checkbox',
                'tab' => 'Image Optimize'
            ],
            'should_resize_thumb' => [
                'name' => 'should_resize_thumb',
                'label' => 'Resize ảnh thumb khi tải về',
                'type' => 'checkbox',
                'tab' => 'Image Optimize'
            ],
            'resize_thumb_width' => [
                'name' => 'resize_thumb_width',
                'label' => 'Chiều rộng ảnh thumb (px)',
                'type' => 'number',
                'default' => 200,
                'attributes' => [
                    'placeholder' => 'Để trống nếu muốn giữ nguyên tỉ lệ',
                    'class'       => 'form-control',
                ],
                'wrapper' => [
                    'class'       => 'form-group col-md-6',
                ],
                'tab' => 'Image Optimize'
            ],
            'resize_thumb_height' => [
                'name' => 'resize_thumb_height',
                'label' => 'Chiều cao ảnh thumb (px)',
                'type' => 'number',
                'attributes' => [
                    'placeholder' => 'Để trống nếu muốn giữ nguyên tỉ lệ',
                    'class'       => 'form-control',
                ],
                'wrapper' => [
                    'class'       => 'form-group col-md-6',
                ],
                'tab' => 'Image Optimize'
            ],
            'should_resize_poster' => [
                'name' => 'should_resize_poster',
                'label' => 'Resize ảnh poster khi tải về',
                'type' => 'checkbox',
                'tab' => 'Image Optimize'
            ],
            'resize_poster_width' => [
                'name' => 'resize_poster_width',
                'label' => 'Chiều rộng ảnh poster (px)',
                'type' => 'number',
                'default' => 300,
                'attributes' => [
                    'placeholder' => 'Để trống nếu muốn giữ nguyên tỉ lệ',
                    'class'       => 'form-control',
                ],
                'wrapper' => [
                    'class'       => 'form-group col-md-6',
                ],
                'tab' => 'Image Optimize'
            ],
            'resize_poster_height' => [
                'name' => 'resize_poster_height',
                'label' => 'Chiều cao ảnh poster (px)',
                'type' => 'number',
                'attributes' => [
                    'placeholder' => 'Để trống nếu muốn giữ nguyên tỉ lệ',
                    'class'       => 'form-control',
                ],
                'wrapper' => [
                    'class'       => 'form-group col-md-6',
                ],
                'tab' => 'Image Optimize'
            ],
            'crawler_schedule_enable' => [
                'name' => 'crawler_schedule_enable',
                'label' => '<b>Bật/Tắt tự động</b>',
                'default' => false,
                'type' => 'checkbox',
                'tab' => 'Schedule'
            ],
            'crawler_schedule_page_from' => [
                'name' => 'crawler_schedule_page_from',
                'label' => 'Trang đầu',
                'type' => 'number',
                'default' => 1,
                'attributes' => [
                    'placeholder' => '1',
                    'class'       => 'form-control',
                ],
                'wrapper' => [
                    'class'       => 'form-group col-md-4',
                ],
                'tab' => 'Schedule'
            ],
            'crawler_schedule_page_to' => [
                'name' => 'crawler_schedule_page_to',
                'label' => 'Trang cuối',
                'type' => 'number',
                'default' => 2,
                'attributes' => [
                    'placeholder' => '2',
                    'class'       => 'form-control',
                ],
                'wrapper' => [
                    'class'       => 'form-group col-md-4',
                ],
                'tab' => 'Schedule'
            ],
            'crawler_schedule_cron_config' => [
                'name'        => 'crawler_schedule_cron_config',
                'label'       => 'Cron config',
                'type'        => 'text',
                'default'     => '* * * * *',
                'hint'        => '<a target="_blank" href="https://crontab.guru/every-10-minutes">See more</a>',
                'attributes' => [
                    'placeholder' => '* * * * * *',
                    'class'       => 'form-control',
                ],
                'wrapper' => [
                    'class'       => 'form-group col-md-4',
                ],
                'tab'   => 'Schedule'
            ],
            'crawler_schedule_excludedType' => [
                'name' => 'crawler_schedule_excludedType',
                'label' => 'Bỏ qua định dạng',
                'type' => 'select_from_array',
                'options'         => ['series' => 'Phim Bộ', 'single' => 'Phim Lẻ', 'hoathinh' => 'Hoạt Hình', 'tvshows' => 'TV Shows'],
                'allows_null'     => false,
                'allows_multiple' => true,
                'tab' => 'Schedule'
            ],
            'crawler_schedule_excludedCategories' => [
                'name' => 'crawler_schedule_excludedCategories',
                'label' => 'Bỏ qua thể loại',
                'type' => 'select_from_array',
                'options'         => $categories,
                'allows_null'     => false,
                'allows_multiple' => true,
                'tab' => 'Schedule'
            ],
            'crawler_schedule_excludedRegions' => [
                'name' => 'crawler_schedule_excludedRegions',
                'label' => 'Bỏ qua quốc gia',
                'type' => 'select_from_array',
                'options'         => $regions,
                'allows_null'     => false,
                'allows_multiple' => true,
                'tab' => 'Schedule'
            ],
            'crawler_schedule_fields' => [
                'name' => 'crawler_schedule_fields',
                'label' => 'Field cập nhật',
                'type' => 'select_from_array',
                'default' => array_keys($fields),
                'options'         => $fields,
                'allows_null'     => false,
                'allows_multiple' => true,
                'tab' => 'Schedule'
            ],
            'crawl_domain' => [
                'name' => 'crawl_domain',
                'label' => 'Crawl Domain',
                'type' => 'text',
                'value' => 'https://example.com',
                'tab' => 'Setting'
            ],
        ];

        return $options;
    }
}
