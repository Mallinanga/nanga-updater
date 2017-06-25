<?php

namespace Nanga\Plugins;

class Plugin
{

    //public $config;
    protected $github_auth;
    protected $github_data;

    public function __construct()
    {
        if (defined('GITHUB_CLIENT_ID') && defined('GITHUB_CLIENT_SECRET')) {
            $this->github_auth = '?client_id=' . GITHUB_CLIENT_ID . '&client_secret=' . GITHUB_CLIENT_SECRET;
        }
        add_filter('pre_set_site_transient_update_plugins', [$this, 'check']);
        add_filter('plugins_api', [$this, 'details'], 10, 3);
        add_filter('upgrader_source_selection', [$this, 'location'], 10, 4);
    }

    public function check($transient)
    {
        if ( ! file_exists(WP_PLUGIN_DIR . '/' . $this->config['plugin_file'])) {
            return $transient;
        }
        if (empty($transient->checked)) {
            return $transient;
        }
        // error_log('--- ' . __FUNCTION__ . ' ' . $this->config['slug']);
        // delete_site_transient($this->config['slug'] . '_latest_tag');
        $this->inject();
        $update = version_compare($this->config['new_version'], $this->config['version'], '>');
        if ($update) {
            $response              = new \stdClass;
            $response->plugin      = $this->config['slug'];
            $response->new_version = $this->config['new_version'];
            $response->slug        = $this->config['slug'];
            $response->url         = $this->config['github_url'];
            $response->package     = $this->config['zip_url'];
            if (false !== $response) {
                $transient->response[$this->config['plugin_file']] = $response;
            }
        }

        return $transient;
    }

    private function inject()
    {
        $this->github();
        $pluginData                   = get_plugin_data(WP_PLUGIN_DIR . '/' . $this->config['plugin_file']);
        $this->config['version']      = $pluginData['Version'];
        $this->config['new_version']  = $this->latest();
        $this->config['last_updated'] = $this->updated();
        $this->config['plugin_name']  = $pluginData['Name'];
        $this->config['description']  = $this->changelog();
        $this->config['author']       = $pluginData['Author'];
        $this->config['homepage']     = $this->config['github_url'];
        $this->config['zip_url']      = trailingslashit($this->config['zip_url']) . $this->config['new_version'];
    }

    private function github()
    {
        if ( ! empty($this->github_data)) {
            return $this->github_data;
        }
        // error_log('--- ' . __FUNCTION__ . ' ' . $this->config['slug']);
        $transient = get_site_transient($this->config['slug'] . '_github_data');
        if ($this->force() || ( ! isset($transient) || ! $transient || '' == $transient)) {
            $request = wp_remote_get($this->config['api_url'] . $this->github_auth);
            if (is_wp_error($request)) {
                return false;
            }
            $data = json_decode($request['body']);
            set_site_transient($this->config['slug'] . '_github_data', $data, WEEK_IN_SECONDS);
        }
        $this->github_data = $transient;

        return $this->github_data;
    }

    private function latest()
    {
        $latest = get_site_transient($this->config['slug'] . '_latest_tag');
        if ($this->force() || empty($latest)) {
            $remoteResponse = wp_remote_get(trailingslashit($this->config['api_url']) . $this->config['channel'] . $this->github_auth);
            if (is_wp_error($remoteResponse)) {
                return false;
            }
            $releases = json_decode($remoteResponse['body']);
            $latest   = false;
            if (is_array($releases)) {
                foreach ($releases as $release) {
                    if ('releases' === $this->config['channel']) {
                        $latest = $release->tag_name;
                    }
                    if ('tags' === $this->config['channel']) {
                        $latest = $release->name;
                    }
                    break;
                }
            }
            if ( ! empty($latest)) {
                set_site_transient($this->config['slug'] . '_latest_tag', $latest, WEEK_IN_SECONDS);
            }
        }

        return $latest;
    }

    private function updated()
    {
        $github = $this->github();

        return ! empty($github->updated_at) ? date('Y-m-d', strtotime($github->updated_at)) : false;
    }

    private function changelog()
    {
        $github = $this->github();

        return ! empty($github->description) ? $github->description : 'CHANGELOG not available.';
    }

    private function force()
    {
        return (defined('NANGA_UPDATER_FORCE_UPDATE') && NANGA_UPDATER_FORCE_UPDATE);
    }

    public function location($source, $remote_source, $upgrader, $hook_extra = null)
    {
        global $wp_filesystem;
        if ($upgrader instanceof \Plugin_Upgrader) {
            if (isset($_REQUEST['slug']) && $_REQUEST['slug'] == $this->config['slug']) {
                $new_source = trailingslashit($remote_source) . $this->config['proper_folder_name'];
                //$wp_filesystem->move($upgrader['destination'], $new_source); // Forces 500 Error
                if ($wp_filesystem->move($source, $new_source, true)) {
                    return trailingslashit($new_source);
                } else {
                    return new \WP_Error();
                }
            }
        }

        return $source;
    }

    public function details($false, $action, $details)
    {
        if (isset($details->slug) && $details->slug === $this->config['slug']) {
            $this->inject();
            $details->slug          = $this->config['slug'];
            $details->plugin        = $this->config['slug'];
            $details->name          = $this->config['plugin_name'];
            $details->plugin_name   = $this->config['plugin_name'];
            $details->version       = $this->config['new_version'];
            $details->author        = $this->config['author'];
            $details->homepage      = $this->config['homepage'];
            $details->tested        = get_bloginfo('version');
            $details->last_updated  = $this->config['last_updated'];
            $details->sections      = [
                'description' => $this->config['description'],
                'changelog'   => $this->changelog(),
            ];
            $details->download_link = $this->config['zip_url'];

            return $details;
        }

        return $false;
    }
}
