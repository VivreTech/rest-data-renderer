<?php


namespace vivretech\rest\renderer;


use Yii;
use yii\base\BaseObject;
use yii\base\UnknownMethodException;


abstract class DataRenderer extends BaseObject
{

    /**
     * @var string the ID of this DataRenderer.
     */
    public $id;

    /* @var string */
    private $renderActionPrefix = 'render';

    /**
     * @var string the ID of the action that is used when the action ID is not specified.
     * Defaults to 'main'.
     */
    public $defaultRenderAction = 'main';


    /**
     * @param string $id the ID of this renderer.
     * @param array $config name-value pairs that will be used to initialize the object properties.
     */
    public function __construct($id = null, $config = [])
    {
        if (empty($id))
        {
            $class = new \ReflectionClass($this);
            $id = $class->getShortName();
        }

        $this->id = $id;
        parent::__construct($config);
    }


    /**
     * Returns the unique ID of the renderer.
     * @return string the renderer ID.
     */
    public function getUniqueId()
    {
        return $this->id;
    }


    /**
     * @param $id
     * @param array $params
     * @return mixed
     */
    public function run($id, $params = [])
    {
        return $this->runRenderAction($id, $params);
    }


    /**
     * @param $id
     * @param $params
     * @return mixed
     */
    public function runRenderAction($id, $params = [])
    {
        $result = null;
        $renderActionMethod = $this->getRenderAction($id);

        if ($renderActionMethod === null)
        {
            throw new UnknownMethodException('Unable to resolve the render: ' . $this->getUniqueId() . '/' . $id);
        }

        Yii::trace('Route to run: ' . $renderActionMethod, __METHOD__);
        Yii::trace('Running action: ' . $this::className() . '::' . $renderActionMethod . '()', __METHOD__);

        $result = call_user_func_array([$this, $renderActionMethod], $params);

        return $result;
    }


    /**
     * @param $id
     * @return null|string
     */
    protected function getRenderAction($id)
    {
        if (empty($id))
        {
            $id = $this->defaultRenderAction;
        }

        $methodName =
            $this->renderActionPrefix .
            str_replace(' ', '', ucwords(implode(' ', explode('-', $id))));

        if (method_exists($this, $methodName))
        {
            $method = new \ReflectionMethod($this, $methodName);

            if ($method->isPublic() && $method->getName() === $methodName)
            {
                return $methodName;
            }
        }

        return null;
    }


    /**
     * @param array $params
     * @return mixed
     */
    abstract public function renderMain($params = []);


}
