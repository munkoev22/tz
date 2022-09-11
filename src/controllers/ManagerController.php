<?php

namespace app\controllers;

use Yii;
use app\models\Manager;
use app\models\Request;
use app\models\ManagerSearch;
use app\models\RequestSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class ManagerController extends Controller
{
    public function actionIndex()
    {
        $searchModel = new ManagerSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

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
        $model = new Manager();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    protected function findModel($id)
    {
        if (($model = Manager::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
   
    public function actionRequests($id){
        return $this->redirect("index.php?r=request%2Fprev-req&manager_id=".$id);
        $searchModel = new RequestSearch();
        $dataProvider = $searchModel->search(['RequestSearch'=>['manager_id'=>$id]]);
        return $this->redirect(['../request/index', 'searchModel' => $searchModel,
                                                    'dataProvider' => $dataProvider]);
        // return $this->render('../request/index', [
        //     'searchModel' => $searchModel,
        //     'dataProvider' => $dataProvider,
        // ]);
        
    }
}
