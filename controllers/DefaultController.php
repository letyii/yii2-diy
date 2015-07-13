<?php
namespace letyii\diy\controllers;

use Yii;
use yii\web\Controller;
use letyii\diy\models\Diy;
use yii\helpers\ArrayHelper;

class DefaultController extends Controller {
    
    public function actionIndex(){
        $queryParams = Yii::$app->request->getQueryParams();
        $searchModel = new Diy;
        $searchModel->scenario = 'search';
        $dataProvider = $searchModel->search($queryParams);
        
        Yii::$app->view->title = Yii::t($this->module->id, ucfirst($this->module->id));
        Yii::$app->view->params['breadcrumbs'][] = Yii::$app->view->title;
        
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel
        ]);
    }
    
    public function actionUpdate($id){
        $model = $this->findModel($id);
        
        if ($model->load(Yii::$app->request->post()) AND $model->save()) {
            return $this->redirect(['update', 'id' => (string)$model->_id]);
        }
        
        Yii::$app->view->title = Yii::t($this->module->id, 'Update');
        Yii::$app->view->params['breadcrumbs'][] = ['label' => Yii::t($this->module->id, ucfirst($this->module->id)), 'url' => ['index']];
        Yii::$app->view->params['breadcrumbs'][] = Yii::$app->view->title;
        
        return $this->render('form', [
            'model' => $model
        ]);
    }
    
    public function actionCreate(){
        $model = new Diy;
        
        if ($model->load(Yii::$app->request->post()) AND $model->save()) {
            return $this->redirect(['update', 'id' => (string)$model->_id]);
        }
        
        Yii::$app->view->title = Yii::t($this->module->id, 'Create');
        Yii::$app->view->params['breadcrumbs'][] = ['label' => Yii::t($this->module->id, ucfirst($this->module->id)), 'url' => ['index']];
        Yii::$app->view->params['breadcrumbs'][] = Yii::$app->view->title;
        
        return $this->render('form', [
            'model' => $model
        ]);
    }
    
    public function actionBuild($id){
        $model = $this->findModel($id);
        
        Yii::$app->view->title = Yii::t($this->module->id, 'Build');
        Yii::$app->view->params['breadcrumbs'][] = ['label' => Yii::t($this->module->id, ucfirst($this->module->id)), 'url' => ['index']];
        Yii::$app->view->params['breadcrumbs'][] = Yii::$app->view->title;
        
        return $this->render('build');
    }
    
    /**
     * Deletes an existing model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
//        if (!Yii::$app->user->can(Diy::moduleName() . ':delete'))
//            return $this->render('//message', ['messages' => ['danger' => Yii::t('yii', 'You are not allowed to perform this action.')]]);

        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }
    
    /**
     * Finds the model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Diy::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}