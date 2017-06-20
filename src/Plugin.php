<?php

namespace Nanga;

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
        add_filter('plugins_api', [$this, 'pluginDetails'], 10, 3);
        add_filter('upgrader_source_selection', [$this, 'location'], 10, 4);
    }

    public function check($transient)
    {
        if (empty($transient->checked)) {
            return $transient;
        }
        delete_site_transient($this->config['slug'] . '_latest_tag');
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
        $pluginData                   = $this->pluginData();
        $this->config['version']      = $pluginData['Version'];
        $this->config['new_version']  = $this->latest();
        $this->config['last_updated'] = date('Y-m-d');
        $this->config['plugin_name']  = $pluginData['Name'];
        $this->config['description']  = $pluginData['Description'];
        $this->config['author']       = $pluginData['Author'];
        $this->config['homepage']     = $this->config['github_url'];
        $this->config['zip_url']      = trailingslashit($this->config['zip_url']) . $this->config['new_version'];
    }

    private function pluginData()
    {
        return get_plugin_data(WP_PLUGIN_DIR . '/' . $this->config['plugin_file']);
    }

    private function latest()
    {
        $latest = get_site_transient($this->config['slug'] . '_latest_tag');
        if ($this->ignoreTransients() || empty($latest)) {
            $remoteResponse = wp_remote_get(trailingslashit($this->config['api_url']) . $this->config['channel'] . $this->github_auth);
            if (is_wp_error($remoteResponse)) {
                error_log(print_r($remoteResponse, true));

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
                set_site_transient($this->config['slug'] . '_latest_tag', $latest, HOUR_IN_SECONDS / 2);
            }
        }

        return $latest;
    }

    private function ignoreTransients()
    {
        return (defined('NANGA_UPDATER_FORCE_UPDATE') && NANGA_UPDATER_FORCE_UPDATE);
    }

    public function pluginDetails($false, $action, $details)
    {
        if ( ! isset($details->slug) || $details->slug != $this->config['slug']) {
            return false;
        }
        $this->inject();
        //$details->requires    = get_bloginfo('version');
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
        ];
        $details->download_link = $this->config['zip_url'];

        return $details;
    }

    public function location($source, $remote_source, $upgrader, $hook_extra = null)
    {
        global $wp_filesystem;
        error_log(print_r($upgrader, true));
        if ($upgrader instanceof \Plugin_Upgrader) {
            //error_log(print_r($hook_extra, true));
            //error_log(print_r($_POST, true));
            if ($_POST['slug'] == $this->config['slug']) {
                //$new_source = WP_PLUGIN_DIR . '/' . trailingslashit($this->config['proper_folder_name']);
                $new_source = trailingslashit($remote_source) . $this->config['proper_folder_name'];
                error_log(print_r($source, true));
                error_log(print_r($new_source, true));
                //$wp_filesystem->move($upgrader['destination'], $new_source); // Forces 500 Error
                if ($wp_filesystem->move($source, $new_source, true)) {

                    return trailingslashit($new_source);
                } else {
                    error_log(print_r($upgrader, true));

                    return new \WP_Error();
                }
            }
        }

        return $source;
    }

    private function date()
    {
        $remoteData = $this->remoteData();

        return ! empty($remoteData->updated_at) ? date('Y-m-d', strtotime($remoteData->updated_at)) : false;
    }

    private function remoteData()
    {
        if ( ! empty($this->github_data)) {
            $remoteData = $this->github_data;
        } else {
            $remoteData = get_site_transient($this->config['slug'] . '_github_data');
            if ($this->ignoreTransients() || ( ! isset($remoteData) || ! $remoteData || '' == $remoteData)) {
                $remoteData = wp_remote_get($this->config['api_url'] . $this->github_auth);
                if (is_wp_error($remoteData)) {
                    error_log(print_r($remoteData, true));

                    return false;
                }
                $remoteData = json_decode($remoteData['body']);
                set_site_transient($this->config['slug'] . '_github_data', $remoteData, WEEK_IN_SECONDS);
            }
            $this->github_data = $remoteData;
        }

        return $remoteData;
    }

    private function description()
    {
        $remoteData = $this->remoteData();

        return ! empty($remoteData->description) ? $remoteData->description : false;
    }
}
