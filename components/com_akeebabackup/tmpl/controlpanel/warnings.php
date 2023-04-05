<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2022 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/** @var $this \Akeeba\Component\AkeebaBackup\Administrator\View\Controlpanel\HtmlView */

// Protect from unauthorized access
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') || die();

$cloudFlareTestFile = 'CLOUDFLARE::' . Uri::base() . 'media/com_akeebabackup/ControlPanel.min.js';
$cloudFlareTestFile .= '?' . ApplicationHelper::getHash(AKEEBABACKUP_VERSION . AKEEBABACKUP_DATE);

$token = Factory::getApplication()->getFormToken();
?>
<?php // Configuration Wizard pop-up ?>
<?php if($this->promptForConfigurationwizard && !defined('AKEEBADEBUG')): ?>
	<?= $this->loadAnyTemplate('Configuration/confwiz_modal') ?>
<?php endif ?>

<?php // Potentially web accessible output directory ?>
<?php if (true||$this->isOutputDirectoryUnderSiteRoot): ?>
    <!--
    Oh, hi there! It looks like you got curious and are peeking around your browser's developer tools – or just the
    source code of the page that loaded on your browser. Cool! May I explain what we are seeing here?

    Just to let you know, the next three DIVs (outDirSystem, insecureOutputDirectory and missingRandomFromFilename) are
    HIDDEN and their existence doesn't mean that your site has an insurmountable security issue. To the contrary.
    Whenever Akeeba Backup detects that the backup output directory is under your site's root it will CHECK its security
    i.e. if it's really accessible over the web. This check is performed with an AJAX call to your browser so if it
    takes forever or gets stuck you won't see a frustrating blank page in your browser. If AND ONLY IF a problem is
    detected said JavaScript will display one of the following DIVs, depending on what is applicable.

    So, to recap. These hidden DIVs? They don't indicate a problem with your site. If one becomes visible then – and
    ONLY then – should you do something about it, as instructed. But thank you for being curious. Curiosity is how you
    get involved with and better at web development. Stay curious!
    -->
	<?php // Web accessible output directory that coincides with or is inside in a CMS system folder ?>
    <div class="alert alert-danger alert-dismissible" id="outDirSystem" style="display: none">
        <h3>
			<?= Text::_('COM_AKEEBABACKUP_CPANEL_HEAD_OUTDIR_INVALID') ?>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="<?= Text::_('JLIB_HTML_BEHAVIOR_CLOSE') ?>"></button>
		</h3>
        <p>
            <?= Text::sprintf('COM_AKEEBABACKUP_CPANEL_LBL_OUTDIR_LISTABLE', realpath($this->getModel()->getOutputDirectory())) ?>
        </p>
        <p>
			<?= Text::_('COM_AKEEBABACKUP_CPANEL_LBL_OUTDIR_ISSYSTEM') ?>
        </p>
        <p>
			<?= Text::_('COM_AKEEBABACKUP_CPANEL_LBL_OUTDIR_ISSYSTEM_FIX') ?>
			<?= Text::_('COM_AKEEBABACKUP_CPANEL_LBL_OUTDIR_DELETEORBEHACKED') ?>
        </p>
    </div>

	<?php // Output directory can be listed over the web ?>
    <div class="alert alert-<?= $this->hasOutputDirectorySecurityFiles ? 'danger' : 'warning' ?>" id="insecureOutputDirectory" style="display: none">
        <h3>
	        <?php if ($this->hasOutputDirectorySecurityFiles): ?>
		        <?= Text::_('COM_AKEEBABACKUP_CPANEL_HEAD_OUTDIR_UNFIXABLE') ?>
            <?php else: ?>
		        <?= Text::_('COM_AKEEBABACKUP_CPANEL_HEAD_OUTDIR_INSECURE') ?>
	        <?php endif ?>
        </h3>
        <p>
            <?= Text::sprintf('COM_AKEEBABACKUP_CPANEL_LBL_OUTDIR_LISTABLE', realpath($this->getModel()->getOutputDirectory())) ?>
        </p>
        <?php if (!$this->hasOutputDirectorySecurityFiles): ?>
        <p>
            <?= Text::_('COM_AKEEBABACKUP_CPANEL_LBL_OUTDIR_CLICKTHEBUTTON') ?>
        </p>
        <p>
            <?= Text::_('COM_AKEEBABACKUP_CPANEL_LBL_OUTDIR_FIX_SECURITYFILES') ?>
        </p>

        <form action="index.php" method="POST" class="akeeba-form--inline">
            <input type="hidden" name="option" value="com_akeebabackup">
            <input type="hidden" name="view" value="Controlpanel">
            <input type="hidden" name="task" value="fixOutputDirectory">
            <input type="hidden" name="<?= $token ?>" value="1">

            <button type="submit" class="btn btn-success w-100">
                <span class="akion-hammer"></span>
				<?= Text::_('COM_AKEEBABACKUP_CPANEL_BTN_FIXSECURITY') ?>
            </button>
        </form>
        <?php else: ?>
        <p>
			<?= Text::_('COM_AKEEBABACKUP_CPANEL_LBL_OUTDIR_TRASHHOST') ?>
			<?= Text::_('COM_AKEEBABACKUP_CPANEL_LBL_OUTDIR_DELETEORBEHACKED') ?>
        </p>
	    <?php endif ?>
    </div>

	<?php // Output directory cannot be listed over the web but I can download files ?>
    <div class="akeeba-block--warning" id="missingRandomFromFilename" style="display: none">
        <h3>
			<?= Text::_('COM_AKEEBABACKUP_CPANEL_HEAD_OUTDIR_INSECURE_ALT') ?>
        </h3>
        <p>
            <?= Text::sprintf('COM_AKEEBABACKUP_CPANEL_LBL_OUTDIR_FILEREADABLE', realpath($this->getModel()->getOutputDirectory())) ?>
        </p>
        <p>
			<?= Text::_('COM_AKEEBABACKUP_CPANEL_LBL_OUTDIR_CLICKTHEBUTTON') ?>
        </p>
        <p>
			<?= Text::_('COM_AKEEBABACKUP_CPANEL_LBL_OUTDIR_FIX_RANDOM') ?>
        </p>

        <form action="index.php" method="POST" class="akeeba-form--inline">
            <input type="hidden" name="option" value="com_akeebabackup">
            <input type="hidden" name="view" value="Controlpanel">
            <input type="hidden" name="task" value="addRandomToFilename">
            <input type="hidden" name="<?= $token ?>" value="1">

            <button type="submit" class="akeeba-btn--block--green">
                <span class="akion-hammer"></span>
				<?= Text::_('COM_AKEEBABACKUP_CPANEL_BTN_FIXSECURITY') ?>
            </button>
        </form>
    </div>

<?php endif ?>

<?php // mbstring warning ?>
<?php if(!$this->checkMbstring): ?>
    <div class="alert alert-warning alert-dismissible">
		<?= Text::sprintf('COM_AKEEBABACKUP_CPANL_ERR_MBSTRING', PHP_VERSION) ?>
		<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="<?= Text::_('JLIB_HTML_BEHAVIOR_CLOSE') ?>"></button>
    </div>
<?php endif ?>

<?php // Front-end backup secret word reminder ?>
<?php if(!empty($this->frontEndSecretWordIssue)): ?>
    <div class="alert alert-danger alert-dismissible">
        <h3>
			<?= Text::_('COM_AKEEBABACKUP_CPANEL_ERR_FESECRETWORD_HEADER') ?>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="<?= Text::_('JLIB_HTML_BEHAVIOR_CLOSE') ?>"></button>
		</h3>
        <p><?= Text::_('COM_AKEEBABACKUP_CPANEL_ERR_FESECRETWORD_INTRO') ?></p>
        <p><?= $this->frontEndSecretWordIssue ?></p>
        <p>
			<?= Text::_('COM_AKEEBABACKUP_CPANEL_ERR_FESECRETWORD_WHATTODO_JOOMLA') ?>
            <?= Text::sprintf('COM_AKEEBABACKUP_CPANEL_ERR_FESECRETWORD_WHATTODO_COMMON', $this->newSecretWord) ?>
        </p>
        <p>
            <a class="btn btn-success btn-lg"
               href="index.php?option=com_akeebabackup&view=Controlpanel&task=resetSecretWord&<?= $token ?>=1">
                <span class="fa fa-sync"></span>
				<?= Text::_('COM_AKEEBABACKUP_CPANEL_BTN_FESECRETWORD_RESET') ?>
            </a>
        </p>
    </div>
<?php endif ?>

<?php // Old PHP version reminder ?>
<?= $this->loadAnyTemplate('Controlpanel/warning_phpversion') ?>

<?php // Wrong media directory permissions ?>
<?php if(!$this->areMediaPermissionsFixed): ?>
    <div id="notfixedperms" class="alert alert-danger alert-dismissible">
        <h3>
			<?= Text::_('COM_AKEEBABACKUP_CONTROLPANEL_WARN_WARNING') ?>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="<?= Text::_('JLIB_HTML_BEHAVIOR_CLOSE') ?>"></button>
		</h3>
        <p><?= Text::_('COM_AKEEBABACKUP_CONTROLPANEL_WARN_PERMS_L1') ?></p>
        <p><?= Text::_('COM_AKEEBABACKUP_CONTROLPANEL_WARN_PERMS_L2') ?></p>
        <ol>
            <li><?= Text::_('COM_AKEEBABACKUP_CONTROLPANEL_WARN_PERMS_L3A') ?></li>
            <li><?= Text::_('COM_AKEEBABACKUP_CONTROLPANEL_WARN_PERMS_L3B') ?></li>
        </ol>
        <p><?= Text::_('COM_AKEEBABACKUP_CONTROLPANEL_WARN_PERMS_L4') ?></p>
    </div>
<?php endif ?>

<?php // You need to enter your Download ID ?>
<?php if($this->needsDownloadID):
	$updateSiteEditUrl = Route::_('index.php?option=com_installer&task=updatesite.edit&update_site_id=' . $this->updateSiteId); ?>
    <div class="alert alert-info alert-dismissible">
        <h3 class="alert-heading">
			<?= Text::_('COM_AKEEBABACKUP_CPANEL_MSG_MUSTENTERDLID') ?>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="<?= Text::_('JLIB_HTML_BEHAVIOR_CLOSE') ?>"></button>
		</h3>
        <p>
            <?= Text::sprintf('COM_AKEEBABACKUP_LBL_CPANEL_NEEDSDLID','https://www.akeeba.com/download/official/add-on-dlid.html') ?>
        </p>
        <p>
			<?= Text::sprintf('COM_AKEEBABACKUP_CPANEL_MSG_WHERETOENTERDLID', $updateSiteEditUrl) ?>
		</p>
        <p class="text-muted">
			<?= Text::_('COM_AKEEBABACKUP_CPANEL_MSG_JOOMLABUGGYUPDATES') ?>
		</p>
    </div>
<?php endif ?>

<?php // You have CORE; you need to upgrade, not just enter a Download ID ?>
<?php if($this->coreWarningForDownloadID): ?>
    <div class="alert alert-warning alert-dismissible">
		<?= Text::sprintf('COM_AKEEBABACKUP_LBL_CPANEL_NEEDSUPGRADE','http://akee.ba/abcoretopro') ?>
		<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="<?= Text::_('JLIB_HTML_BEHAVIOR_CLOSE') ?>"></button>
    </div>
<?php endif ?>

<?php // Upgrade from Akeeba Backup 7 or 8? ?>
<?php if ($this->canUpgradeFromAkeebaBackup8): ?>
	<div class="alert alert-info alert-dismissible">
		<h3>
			<?= Text::_('COM_AKEEBABACKUP_LBL_CPANEL_UPGRADE_FROM_AKEEBABACKUP8_HEAD') ?>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="<?= Text::_('JLIB_HTML_BEHAVIOR_CLOSE') ?>"></button>
		</h3>
		<p>
			<?= Text::_('COM_AKEEBABACKUP_LBL_CPANEL_UPGRADE_FROM_AKEEBABACKUP8_BODY') ?>
		</p>
		<p>
			<a class="btn btn-primary btn-lg"
			   href="<?= Route::_('index.php?option=com_akeebabackup&view=Upgrade') ?>"
			>
				<span class="fa fa-hat-wizard"></span>
				<?= Text::_('COM_AKEEBABACKUP_LBL_CPANEL_UPGRADE_FROM_AKEEBABACKUP8_BTN') ?>
			</a>
		</p>
	</div>
<?php elseif(!empty($this->akeebaBackup8PackageId)): ?>
	<div class="alert alert-info alert-dismissible">
		<h3>
			<?= Text::_('COM_AKEEBABACKUP_LBL_CPANEL_UPGRADED_FROM_AKEEBABACKUP8_HEAD') ?>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="<?= Text::_('JLIB_HTML_BEHAVIOR_CLOSE') ?>"></button>
		</h3>
		<p>
			<?= Text::_('COM_AKEEBABACKUP_LBL_CPANEL_UPGRADE_FROM_AKEEBABACKUP8_BODY') ?>
		</p>
		<p class="small">
			<?= Text::_('COM_AKEEBABACKUP_LBL_CPANEL_UPGRADED_FROM_AKEEBABACKUP8_NOTE') ?>
		</p>
		<p>
			<a class="btn btn-primary btn-lg"
			   href="<?= Uri::base() ?>index.php?option=com_installer&view=manage&filter[search]=id:<?= $this->akeebaBackup8PackageId ?>"
			>
				<span class="fa fa-trash"></span>
				<?= Text::_('COM_AKEEBABACKUP_LBL_CPANEL_UPGRADED_FROM_AKEEBABACKUP8_BTN') ?>
			</a>
			<a class="btn btn-warning btn-sm"
			   href="<?= Route::_('index.php?option=com_akeebabackup&view=Upgrade') ?>"
			>
				<span class="fa fa-hat-wizard"></span>
				<?= Text::_('COM_AKEEBABACKUP_LBL_CPANEL_UPGRADE_FROM_AKEEBABACKUP8_BTN') ?>
			</a>
		</p>
	</div>

<?php endif; ?>

<?php // Warn about CloudFlare Rocket Loader ?>
<div class="alert alert-danger alert-dismissible" style="display: none;" id="cloudFlareWarn">
    <h3>
		<?= Text::_('COM_AKEEBABACKUP_CPANEL_MSG_CLOUDFLARE_WARN')?>
		<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="<?= Text::_('JLIB_HTML_BEHAVIOR_CLOSE') ?>"></button>
	</h3>
    <p><?= Text::sprintf('COM_AKEEBABACKUP_CPANEL_MSG_CLOUDFLARE_WARN1', 'https://support.cloudflare.com/hc/en-us/articles/200169456-Why-is-JavaScript-or-jQuery-not-working-on-my-site-') ?></p>
</div>
<?php
/**
 * DO NOT REMOVE THE ATTRIBUTES.
 *
 * This is a specialised test which looks for CloudFlare's completely broken RocketLoader feature and warns the user
 * about it.
 */

$js = <<< JS
window.addEventListener('DOMContentLoaded', function() {
	var test = localStorage.getItem('$cloudFlareTestFile');
	if (test)
	{
		document.getElementById("cloudFlareWarn").style.display = "block";
	}
});

JS;

$this->document->getWebAssetManager()->addInlineScript($js, [], [
		'type' => 'text/javascript',
		'data-cfasync' => 'true'
])
?>