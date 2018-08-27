<?php

namespace LaravelAdminPanel\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use LaravelAdminPanel\Facades\Admin;
use LaravelAdminPanel\FormFields\AbstractHandler;

class SettingsController extends BaseController
{
    public function index()
    {
        // Check permission
        $this->authorize('browse', Admin::model('Setting'));

        $data = Admin::model('Setting')->orderBy('order', 'ASC')->get();

        $settings = [];
        $settings[__('admin.settings.group_general')] = [];
        foreach ($data as $d) {
            if ($d->group == '' || $d->group == __('admin.settings.group_general')) {
                $settings[__('admin.settings.group_general')][] = $d;
            } else {
                $settings[$d->group][] = $d;
            }
        }
        if (count($settings[__('admin.settings.group_general')]) == 0) {
            unset($settings[__('admin.settings.group_general')]);
        }

        $groups_data = Admin::model('Setting')->select('group')->distinct()->get();
        $groups = [];
        foreach ($groups_data as $group) {
            if ($group->group != '') {
                $groups[] = $group->group;
            }
        }

        return Admin::view('admin::settings.index', compact('settings', 'groups'));
    }

    public function store(Request $request)
    {
        // Check permission
        $this->authorize('add', Admin::model('Setting'));

        $key = implode('.', [str_slug($request->input('group')), $request->input('key')]);
        $key_check = Admin::model('Setting')->where('key', $key)->get()->count();

        if ($key_check > 0) {
            return back()->with([
                'message'    => __('admin.settings.key_already_exists', ['key' => $key]),
                'alert-type' => 'error',
            ]);
        }

        $lastSetting = Admin::model('Setting')->orderBy('order', 'DESC')->first();

        if (is_null($lastSetting)) {
            $order = 0;
        } else {
            $order = intval($lastSetting->order) + 1;
        }

        $request->merge(['order' => $order]);
        $request->merge(['value' => '']);
        $request->merge(['key' => $key]);

        Admin::model('Setting')->create($request->all());

        return back()->with([
            'message'    => __('admin.settings.successfully_created'),
            'alert-type' => 'success',
        ]);
    }

    public function update(Request $request)
    {
        // Check permission
        $this->authorize('edit', Admin::model('Setting'));

        $settings = Admin::model('Setting')->all();

        foreach ($settings as $setting) {
            $row = (object) [
                'type'    => $setting->type,
                'field'   => str_replace('.', '_', $setting->key),
                'details' => $setting->details,
                'group'   => $setting->group,
            ];

            $handler = AbstractHandler::initial($row->type);
            $content = $handler->getContentBasedOnType($request, 'settings', $row);

            if ($content === null && isset($setting->value)) {
                $content = $setting->value;
            }

            $key = preg_replace('/^'.str_slug($setting->group).'./i', '', $setting->key);

            $setting->group = $request->input(str_replace('.', '_', $setting->key).'_group');
            $setting->key = implode('.', [str_slug($setting->group), $key]);
            $setting->value = $content;
            $setting->save();
        }

        return back()->with([
            'message'    => __('admin.settings.successfully_saved'),
            'alert-type' => 'success',
        ]);
    }

    public function delete($id)
    {
        // Check permission
        $this->authorize('delete', Admin::model('Setting'));

        Admin::model('Setting')->destroy($id);

        return back()->with([
            'message'    => __('admin.settings.successfully_deleted'),
            'alert-type' => 'success',
        ]);
    }

    public function move_up($id)
    {
        // Check permission
        Admin::canOrFail('edit_settings');

        $setting = Admin::model('Setting')->find($id);

        // Check permission
        $this->authorize('browse', $setting);

        $swapOrder = $setting->order;
        $previousSetting = Admin::model('Setting')
                            ->where('order', '<', $swapOrder)
                            ->where('group', $setting->group)
                            ->orderBy('order', 'DESC')->first();
        $data = [
            'message'    => __('admin.settings.already_at_top'),
            'alert-type' => 'error',
        ];

        if (isset($previousSetting->order)) {
            $setting->order = $previousSetting->order;
            $setting->save();
            $previousSetting->order = $swapOrder;
            $previousSetting->save();

            $data = [
                'message'    => __('admin.settings.moved_order_up', ['name' => $setting->display_name]),
                'alert-type' => 'success',
            ];
        }

        return back()->with($data);
    }

    public function delete_value($id)
    {
        $setting = Admin::model('Setting')->find($id);

        // Check permission
        $this->authorize('delete', $setting);

        if (isset($setting->id)) {
            // If the type is an image... Then delete it
            if ($setting->type == 'image') {
                if (Storage::disk(config('admin.storage.disk'))->exists($setting->value)) {
                    Storage::disk(config('admin.storage.disk'))->delete($setting->value);
                }
            }
            $setting->value = '';
            $setting->save();
        }

        return back()->with([
            'message'    => __('admin.settings.successfully_removed', ['name' => $setting->display_name]),
            'alert-type' => 'success',
        ]);
    }

    public function move_down($id)
    {
        // Check permission
        Admin::canOrFail('edit_settings');

        $setting = Admin::model('Setting')->find($id);

        // Check permission
        $this->authorize('browse', $setting);

        $swapOrder = $setting->order;

        $previousSetting = Admin::model('Setting')
                            ->where('order', '>', $swapOrder)
                            ->where('group', $setting->group)
                            ->orderBy('order', 'ASC')->first();
        $data = [
            'message'    => __('admin.settings.already_at_bottom'),
            'alert-type' => 'error',
        ];

        if (isset($previousSetting->order)) {
            $setting->order = $previousSetting->order;
            $setting->save();
            $previousSetting->order = $swapOrder;
            $previousSetting->save();

            $data = [
                'message'    => __('admin.settings.moved_order_down', ['name' => $setting->display_name]),
                'alert-type' => 'success',
            ];
        }

        return back()->with($data);
    }
}
