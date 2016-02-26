<?php
/**
 * 自动生成缩略名
 *
 * @package BaiduSlug
 * @author Chuck
 * @version 1.0
 * @link http://blog.tapasy.com/baiduslug.html
 */
class BaiduSlug_Plugin implements Typecho_Plugin_Interface
{
    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     *
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function activate()
    {
        if (false == Typecho_Http_Client::get()) {
            throw new Typecho_Plugin_Exception(_t('对不起, 您的主机不支持 php-curl 扩展而且没有打开 allow_url_fopen 功能, 无法正常使用此功能'));
        }

        Typecho_Plugin::factory('admin/write-post.php')->bottom_20 = array('BaiduSlug_Plugin', 'ajax');
        Typecho_Plugin::factory('admin/write-page.php')->bottom_20 = array('BaiduSlug_Plugin', 'ajax');

        Helper::addAction('baidu-slug', 'BaiduSlug_Action');

        return _t('请配置此插件的API KEY, 以使您的插件生效');
    }

    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     *
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function deactivate()
    {
        Helper::removeAction('baidu-slug');
    }

    /**
     * 获取插件配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form)
    {
        /** 生成模式 */
        $mode = new Typecho_Widget_Helper_Form_Element_Radio(
            'mode',
            array(
                'baidu' => _t('百度翻译'),
                'pinyin' => _t('拼音')
            ),
            'baidu',
            _t('生成模式'),
            _t('百度翻译模式都需要填写相应的API和KEY，拼音模式不需要！')
        );
        $form->addInput($mode);

        /** 百度翻译 */
        $bdappid = new Typecho_Widget_Helper_Form_Element_Text(
            'bdappid', NULL, '',
            _t('百度翻译 Appid')
        );
        $form->addInput($bdappid);

        $bdkey = new Typecho_Widget_Helper_Form_Element_Text(
            'bdkey', NULL, '',
            _t('百度翻译API Key'),
            _t('<a href="http://api.fanyi.baidu.com/api/trans/product/index">获取 API Key</a>')
        );
        $form->addInput($bdkey);
    }

    /**
     * 个人用户的配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form
     * @return void
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form){}

    /**
     * ajax执行action
     *
     * @access public
     * @param array $contents 文章输入信息
     * @return void
     */
    public static function ajax()
    {
        Typecho_Widget::widget('Widget_Options')->to($options);
?>
<script>
    
function baiduSlug() {
    var title = $('#title');
    var slug = $('#slug');

    if (slug.val().length > 0 || title.val().length == 0) {
        return;
    }

    $.ajax({
        url: '<?php $options->index('/action/baidu-slug?q='); ?>' + title.val(),
        success: function(data) {
            if (data.result.length > 0) {
                slug.val(data.result).focus();
                slug.siblings('pre').text(data.result);
            }
        }
    });
}

$(function() {
    $('#title').blur(baiduSlug);
    $('#slug').blur(baiduSlug);
});
</script>
<?php
    }
}

