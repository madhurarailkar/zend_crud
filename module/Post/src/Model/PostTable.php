<?php
namespace Post\Model;

use Zend\Db\TableGateway\TableGatewayInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Paginator\Paginator;
use Zend\Db\Sql\Select;
 use Zend\Paginator\Adapter\DbSelect;

class PostTable
{
    protected $tableGateway;

    function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway=$tableGateway;
    }
    public function fetchAll($paginated = false,$keyword = null)
    {
        if ($paginated) {
            return $this->fetchPaginatedResults($keyword);
        }

        return $this->tableGateway->select();
    }
    private function fetchPaginatedResults($keyword)
    {
        // Create a new Select object for the table:
        $select = new Select($this->tableGateway->getTable());
        
        if (!is_null($keyword)) {
            // $select->Where->like('title', '%'.$keyword.'%')
            //     ->orWhere->like('description', '%'.$keyword.'%')
            //     ->orWhere->like('category', '%'.$keyword.'%');
            $select->where('title LIKE "%'.$keyword.'%" OR description LIKE "%'.$keyword.'%" OR category LIKE "%'.$keyword.'%"');
            // $select->where('title LIKE ? OR description LIKE ?', array('%'.$keyword.'%', '%'.$keyword.'%'));

        }
        echo $this->tableGateway->getSql()->getSqlstringForSqlObject($select);

        $rowset = $this->tableGateway->selectWith($select);
        // Create a new result set based on the Album entity:
        $resultSetPrototype = new ResultSet();
        $resultSetPrototype->setArrayObjectPrototype(new Post());

        // Create a new pagination adapter object:
        $paginatorAdapter = new DbSelect(
            // our configured select object:
            $select,
            // the adapter to run it against:
            $this->tableGateway->getAdapter(),
            // the result set to hydrate:
            $resultSetPrototype
        );

        $paginator = new Paginator($paginatorAdapter);
        return $paginator;
    }

    public function saveData($post)
    {
        $data=[
            'title'=>$post->getTitle(),
            'description'=>$post->getDescription(),
            'category'=>$post->getCategory(),

        ];
        if($post->getId()){
            $this->tableGateway->update($data,['id' => $post->getId()]);
        }
        else
        {
            $this->tableGateway->insert($data);
        }
    }

    public function getPost($id){
        $data=$this->tableGateway->select([
            'id'=>$id
        ]);
        return $data->current();
    }
    
    public function deletePost($id){
        $this->tableGateway->delete([
            'id'=>$id
        ]);
    }
}