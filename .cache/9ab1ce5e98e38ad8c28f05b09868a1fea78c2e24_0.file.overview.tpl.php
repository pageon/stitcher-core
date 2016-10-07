<?php
/* Smarty version 3.1.30, created on 2016-10-07 15:43:08
  from "/sites/stitcher/stitcher/tests/src/template/churches/overview.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_57f7c28c084e59_52532172',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '9ab1ce5e98e38ad8c28f05b09868a1fea78c2e24' => 
    array (
      0 => '/sites/stitcher/stitcher/tests/src/template/churches/overview.tpl',
      1 => 1475854983,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:index.tpl' => 1,
  ),
),false)) {
function content_57f7c28c084e59_52532172 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, true);
?>


<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_11142399957f7c28c084252_25293167', 'content');
?>

<?php $_smarty_tpl->inheritance->endChild();
$_smarty_tpl->_subTemplateRender("file:index.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 2, false);
}
/* {block 'content'} */
class Block_11142399957f7c28c084252_25293167 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

    overview
<?php
}
}
/* {/block 'content'} */
}
