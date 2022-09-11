<?php

namespace app\controllers;

use Yii;
use app\models\Request;
use app\models\RequestSearch;
use app\models\Manager;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class RequestController extends Controller
{
    public function actionIndex()
    {
        
        $searchModel = new RequestSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionPrevReq($manager_id){
        $searchModel = new RequestSearch();
        $dataProvider = $searchModel->search(['RequestSearch'=>['manager_id'=>$manager_id]]);
         return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate()
    {
        $model = new Request();
        if($model->load(Yii::$app->request->post()) && $model->save()) {
            $prev = $model->hasDuplicates();
            if(!is_null($prev) && Manager::isWork($prev->manager_id)){
                $model->manager_id = $prev->manager_id;
                
            }
            else{
                $query="SELECT m.*,COUNT(r.id) as _count
                        FROM managers as m LEFT JOIN requests as r on m.id = r.manager_id
                        WHERE m.is_works = true
                        GROUP BY m.id
                        ORDER BY _count 
                        LIMIT 1";
                
			    $managers=Yii::$app->db->createCommand($query)->queryAll();
                $model->manager_id = $managers[0]['id'];
                
            }
            if($model->save(false)){
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }
        else{

            
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save(false)) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    protected function findModel($id)
    {
        if (($model = Request::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    public static function hasDuplicates($id){
        $model = Request::findOne($id);
        $prev = Request::find()->where(['or', ['phone'=>$model->phone], ['email' => $model->email]])
                                ->andWhere(['not in', 'id', $model->id])
                                ->andWhere(['<', 'created_at', $model->created_at])
                                ->orderBy(['created_at' => SORT_DESC])
                                ->one();   
        $days = (strtotime($model->created_at) - strtotime($prev->created_at)) / 3600/24;
        if($days <= 30){
            return $prev;
        }
        else{
            return null;
        }
    }
    public function actionPrev($id){
        $model = Request::findOne($id);
        $prev = Request::find()->where(['or', ['phone'=>$model->phone], ['email' => $model->email]])
                                ->andWhere(['not in', 'id', $model->id])
                                ->andWhere(['<', 'created_at', $model->created_at])
                                ->orderBy(['created_at' => SORT_DESC])
                                ->one();  
        return $this->render('view', [
            'model' =>$prev,
        ]);

    }
}
