<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Post\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\SessionManager;
use Zend\Session\Container;

class IndexController extends AbstractActionController
{
    protected $table;
    public function __construct($table)
    {
        $this->table=$table;
    }
    public function indexAction()
    {   
        //     // Grab the paginator from the AlbumTable:

        // // Set the current page to what has been passed in query string,
        // // or to 1 if none is set, or the page is invalid:
        // $page = (int) $this->params()->fromQuery('page', 1);
        // $page = ($page < 1) ? 1 : $page;
        // $paginator->setCurrentPageNumber($page);

        // // Set the number of items per page to 10:
        // $paginator->setItemCountPerPage(2);

        // return new ViewModel(['paginator' => $paginator]);

         
         // Getting search keyword if any
         $searchKeyword = (string) $this->params()->fromQuery('search_keyword', false);
        //  $search = new Container('PSearch');

        //     if($searchKeyword != null){
        //         $search->psearch = $searchKeyword;
        //     }else{
        //     $searchKeyword = $search->psearch;
        //     }
         $paginator = $this->table->fetchAll(true,$searchKeyword);

         $page = (int) $this->params()->fromQuery('page', 1);
         $page = ($page < 1) ? 1 : $page;
         $paginator->setCurrentPageNumber($page);
 
         // Set the number of items per page to 10:
         $paginator->setItemCountPerPage(2);
 
         return new ViewModel([
             'paginator' => $paginator,
             'searchKeyword' => $searchKeyword,
         ]);       
     
    }
    public function addAction()
    {
        $form=new \Post\Form\PostForm;
        $request=$this->getRequest();
        if(!$request->isPost()){
            return new ViewModel(['form'=>$form]);
        }
        $post=new \Post\Model\Post();
        $form->setData($request->getPost());
        if(!$form->isValid()){
               exit('id is not correct');
        }
        $post->exchangeArray($form->getData());
        $this->table->saveData($post);
        return $this->redirect()->toRoute('home',[
            'controller' => 'home',
            'action' => 'add'
        ]);
    }
    public function viewAction()
    {
        $id=(int) $this->params()->fromRoute('id',0);
        if($id==0){
            exit('Error');
        }
        try{
            $post=$this->table->getPost($id);
        }
        catch(Exception $e){
            exit('Error');
        }

        return new ViewModel([
            'post' => $post,
            'id'=> $id,
        ]);
    }
    public function editAction()
    {

        $id=(int) $this->params()->fromRoute('id',0);
        if($id==0){
            exit('Error');
        }
        try{
            $post=$this->table->getPost($id);
        }
        catch(Exception $e){
            exit('Error');
        }
        $form= new \Post\Form\PostForm();
        $form->bind($post);
        $request=$this->getRequest();
        if(!$request->isPost())
        {
            return new ViewModel([
                'form' => $form,
                'id'=> $id,
            ]);    
        }
        $form->setData($request->getPost());
        if(!$form->isValid()){
            exit('Error');
        }
        $this->table->saveData($post);
        return $this->redirect->toRoute('home',[
            'controller'=>'edit',
            'action'=>'edit',
            'id'=>$id
        ]);
       
    }
    public function deleteAction()
    {
        $id=(int) $this->params()->fromRoute('id',0);
        if($id==0){
            exit('Error');
        }
        try{
            $post=$this->table->getPost($id);
        }
        catch(Exception $e){
            exit('Error');
        }
        $request=$this->getRequest();
        if(!$request->isPost()){
            return new ViewModel([
                'post'=>$post,
                'id'=>$id
            ]);
        }
        $delete=$request->getPost('delete','No');
        if($delete =='Yes')
        {
            $id=(int) $post->getId();
            $this->table->deletePost($id);
            return $this->redirect()->toRoute('home');
        }
        else
        {
            return $this->redirect()->toRoute('home');
        }
    }
    public function sort()
    {
        $viewModel = new ViewModel();
        $viewModel->setTemplate('post/index/add');
        return $viewModel;    
    }
}
