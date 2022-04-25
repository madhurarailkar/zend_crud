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
         $searchKeyword = (string) $this->params()->fromQuery('search_keyword', false);
         $sort_by = (string) $this->params()->fromQuery('sort_by', false);
         $sort = (string) $this->params()->fromQuery('sort', false);

         $paginator = $this->table->fetchAll(true,$searchKeyword,$sort_by,$sort);
         $page = (int) $this->params()->fromQuery('page');
         $page = ($page < 1) ? 1 : $page;
         $paginator->setCurrentPageNumber($page);
         $paginator->setItemCountPerPage(3);
         
         

         return new ViewModel([
             'paginator' => $paginator,
             'searchKeyword' => $searchKeyword,
             'sort_by'=>$sort_by,
             'sort'=>$sort,
             'page'=>$page,
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
    public function sortAction()
    {

        $searchKeyword = (string) $this->params()->fromQuery('search_keyword', false);
        $sort_by = (string) $this->params()->fromQuery('sort_by', false);
        $sort = (string) $this->params()->fromQuery('sort', false);
        if($sort_by=='')
        {
            $sort_by='desc';
        }
        else if($sort_by=='asc')
        {
            $sort_by='desc';
        }
        else if ($sort_by='desc'){
            $sort_by='asc';
        }
        if($sort=='')
         {
             $sort='id';
         }
        $paginator = $this->table->sortBy($searchKeyword,$sort_by,$sort);

        $page = (int) $this->params()->fromQuery('page');
        $page = ($page < 1) ? 1 : $page;
        $paginator->setCurrentPageNumber($page);

        // Set the number of items per page to 10:
        $paginator->setItemCountPerPage(3);
        $viewModel = new ViewModel([
            'paginator' => $paginator,
            'searchKeyword' => $searchKeyword,
            'sort_by'=>$sort_by,
            'sort'=>$sort,
            'page'=>$page,
        ]);       

        $viewModel->setTemplate('post/index/index');
        return $viewModel;  
        // return getResponse();  
    }
}
