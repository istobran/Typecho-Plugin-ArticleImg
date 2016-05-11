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
        $info = ArticleImg_Plugin::sqlInstall();
        Typecho_Plugin::factory('admin/write-post.php')->option = array(__CLASS__, 'setThumbnail');
        Typecho_Plugin::factory('admin/write-page.php')->option = array(__CLASS__, 'setThumbnail');
        Typecho_Plugin::factory('Widget_Contents_Post_Edit')->finishPublish = array(__CLASS__, "changeURL");
        return _t($info);
    }

    /**
     * 把文章顶部图片的 URL 设置装入文章编辑页
     *
     * @access public
     * @return void
     */
    public static function setThumbnail($post) {
      $db = Typecho_Db::get();
      $row = $db->fetchRow($db->select('thumb')->from('table.contents')->where('cid = ?', $post->cid));
      $html = '
      <section class="typecho-post-option">
        <label for="thumbnail-url" class="typecho-label">文章顶部图片URL</label>
        <p><input id="thumbnail-url" name="thumbnail-url" type="text" value="'.$row['thumb'].'" class="w-100 text" /></p>
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
    }

    //SQL创建
    public static function sqlInstall()
    {
      $db = Typecho_Db::get();
      $type = explode('_', $db->getAdapterName());
      $type = array_pop($type);
      $prefix = $db->getPrefix();
      try {
        $select = $db->select('table.contents.thumb')->from('table.contents');
        $db->query($select, Typecho_Db::READ);
        return '检测到图片url字段，插件启用成功';
      } catch (Typecho_Db_Exception $e) {
        $code = $e->getCode();
        if(('Mysql' == $type && (1054 == $code || '42S22' == $code)) ||
            ('SQLite' == $type && ('HY000' == $code || 1 == $code))) {
          try {
            if ('Mysql' == $type) {
              $db->query("ALTER TABLE `".$prefix."contents` ADD `thumb` VARCHAR(255) NOT NULL  DEFAULT 'unknown' COMMENT '文章顶部缩略图URL';");
            } else if ('SQLite' == $type) {
              $db->query("ALTER TABLE `".$prefix."contents` ADD `thumb` VARCHAR(255) NOT NULL  DEFAULT 'unknown'");
            } else {
              throw new Typecho_Plugin_Exception('不支持的数据库类型：'.$type);
            }
            return '建立图片url字段，插件启用成功';
          } catch (Typecho_Db_Exception $e) {
            $code = $e->getCode();
            if(('Mysql' == $type && 1060 == $code) ) {
              return '图片url字段已经存在，插件启用成功';
            }
            throw new Typecho_Plugin_Exception('插件启用失败。错误号：'.$code);
          }
        }
        throw new Typecho_Plugin_Exception('数据表检测失败，插件启用失败。错误号：'.$code);
      }
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
     * 发布文章同时更新URL
     *
     * @access public
     * @return void
     */
    public static function changeURL($contents, $post)
    {
      $thumburl = $post->request->get('thumbnail-url', _t("unknown"));
  		$db = Typecho_Db::get();
      $sql = $db->update('table.contents')->rows(array('thumb' => $thumburl))->where('cid = ?', $post->cid);
  		$db->query($sql);
    }

    /**
     * 前台输出url
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
