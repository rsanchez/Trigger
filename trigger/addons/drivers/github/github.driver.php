<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Trigger GitHub Addon Installer Driver
 *
 * @package		Trigger
 * @author		Rob Sanchez
 */
class Driver_github
{
	public $driver_version = '1.0.0';
	
	public $driver_slug = 'github';
	
	protected $has_github_addon_installer = FALSE;
	
	protected $manifest;

	// --------------------------------------------------------------------------

	public function __construct()
	{
		$this->EE =& get_instance();
		
		if ($this->has_github_addon_installer = (is_dir(PATH_THIRD.'github_addon_installer/')))
		{
			$this->manifest = json_decode(file_get_contents(PATH_THIRD.'github_addon_installer/config/manifest.js'), TRUE);
			
			ksort($this->manifest);
			
			$this->EE->lang->loadfile('github_addon_installer', 'github_addon_installer');
		}
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Install an addon
	 */
	public function _comm_install($addon = '')
	{
		//@TODO
		if ( ! $this->has_github_addon_installer)
		{
			return lang('github.not_installed');
		}
		
		if (empty($addon))
		{
			return lang('github.not_specified');
		}
		
		if ( ! isset($this->manifest[$addon]))
		{
			return lang('github.not_found');
		}
		
		$params = $this->manifest[$addon];
		
		$params['name'] = $addon;
		
		$this->EE->load->add_package_path(PATH_THIRD.'github_addon_installer/');
		
		$this->EE->load->library('github_addon_installer');
		
		$repo = $this->EE->github_addon_installer->repo($params);
		
		return ($repo->install()) ? sprintf(lang('successfully_installed'), $addon) : implode(', ', $repo->errors());
	}
	
	/**
	 * An alias for _comm_install
	 */
	public function _comm_update($addon = '')
	{
		return $this->_comm_install($addon);
	}
	
	public function _comm_search($keyword = '')
	{
		$search = array();
		
		$keywords = preg_split('#\s+#', trim($keyword));
		
		foreach ($this->manifest as $addon => $params)
		{
			$params[] = $addon;
			
			foreach ($params as $value)
			{
				foreach ($keywords as $keyword)
				{
					if (strpos($value, $keyword) !== FALSE)
					{
						$search[] = $addon;
						
						break 2;
					}
				}
			}
		}
		
		return ($search) ? implode("\n", $search) : lang('github.no_search_results');
	}
}

/* End of file github.driver.php */