<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
/**
 * 文章顶部图片插件
 *
 * @package ArticleImg
 * @author BangZ
 * @version 1.0.0
 * @link http://bangz.me
 */
class ArticleImg_Plugin implements Typecho_Plugin_Interface
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
        Typecho_Plugin::factory('admin/write-post.php')->option = array(__CLASS__, 'setThumbnail');
        Typecho_Plugin::factory('admin/write-page.php')->option = array(__CLASS__, 'setThumbnail');
    }

    /**
     * 把文章顶部图片的 URL 设置装入文章编辑页
     *
     * @access public
     * @return void
     */
    public static function setThumbnail($post) {
      $html = '
      <section class="typecho-post-option">
        <label for="thumbnail-url" class="typecho-label">文章顶部图片URL</label>
        <p><input id="article-thumbnail" name="thumbnail-url" type="text" value="" class="w-100 text" /></p>
      </section>
      ';
      _e($html);
    }

    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     *
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function deactivate(){}

    /**
     * 获取插件配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form)
    {
        /** 分类名称 */
        $name = new Typecho_Widget_Helper_Form_Element_Text('word', NULL, 'Hello World', _t('说点什么'));
        $form->addInput($name);
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
     * 插件实现方法
     *
     * @access public
     * @return void
     */
    public static function render()
    {
        // echo '<span class="message success">'
        //     . htmlspecialchars(Typecho_Widget::widget('Widget_Options')->plugin('HelloWorld')->word)
        //     . '</span>';
    }
}
