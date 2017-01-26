<?php
namespace Novice;

use Symfony\Component\HttpFoundation\StreamedResponse;
use Novice\Templating\TemplatingInterface;

class StreamedErrorResponse extends StreamedResponse
{
	/**
     * @var Novice\Templating\TemplatingInterface
     */
	private $templating;

	private $defaultTemplate = '/Resources/views/Exception/error.tpl';
	
	/**
     * Constructor.
     *
     * @param Novice\Templating\TemplatingInterface $templating 
     * @param int									$status   The response status code
     * @param array									$headers  An array of response headers
     *
     * @api
     */
    public function __construct(TemplatingInterface $templating, $status = 404, $headers = array())
    {
		$this->templating = $templating;
        parent::__construct(array($templating, "getGeneratedPage"), $status, $headers);
    }

    /**
     * Factory method for chainability
     *
     * @param Novice\Templating\TemplatingInterface $templating 
     * @param int									$status   The response status code
     * @param array									$headers  An array of response headers
     *
     * @return StreamedErrorResponse
     */
    public static function createResponse(TemplatingInterface $templating, $status = 404, $headers = array())
    {
        return new static($templating, $status, $headers);
    }

	/**
     * {@inheritdoc}
     *
     * This method only sends the content once.
     */
    public function sendContent()
    {
		if($this->templating->templateExists('file:[errors]'.$this->statusCode.'.tpl')){
			$template = 'file:[errors]'.$this->statusCode.'.tpl';
		}
		else if($this->templating->templateExists('file:[errors]error.tpl')){
			$template = 'file:[errors]error.tpl';
		}
		else{
			$template = __DIR__.$this->defaultTemplate;
		}
		$this->templating->setContentFile($template);

		$this->templating->assign(array(
			'status_code' => $this->statusCode,
			'status_text' => $this->statusText,
			));

		parent::sendContent();
    }
}