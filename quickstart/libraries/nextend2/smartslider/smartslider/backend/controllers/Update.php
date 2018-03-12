<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php
class N2SmartsliderBackendUpdateController extends N2SmartSliderController
{

    public function actionCheck() {
        if ($this->validateToken()) {
            $status = N2SmartsliderUpdateModel::getInstance()
                                              ->check();

            $hasError = N2SS3::hasApiError($status);
            if (is_array($hasError)) {
                $this->redirect($hasError);
            }

            $this->redirectToSliders();
        } else {
            $this->refresh();
        }
    }


    public function actionUpdate() {
        if ($this->validateToken()) {
            $status = N2SmartsliderUpdateModel::getInstance()
                                              ->update();

            $hasError = N2SS3::hasApiError($status);
            if (is_array($hasError)) {
                $this->redirect($hasError);
            } else if ($hasError === false) {
                N2Message::success(n2_('Smart Slider 3 updated to the latest version!'));
            }

            $this->redirectToSliders();
        } else {
            $this->refresh();
        }
    }

}
