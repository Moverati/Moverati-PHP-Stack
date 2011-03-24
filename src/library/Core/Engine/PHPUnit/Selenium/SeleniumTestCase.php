<?php
/**
 * Core Action
 *
 * LICENSE
 *
 * This file is intellectual property of Core Action, LLC and may not
 * be used without permission.
 *
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */

namespace Core\Engine\PHPUnit\Selenium;

/**
 * Selenium test
 *
 * @author    Geoffrey Tran
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 *
 * @method unknown  addLocationStrategy()
 * @method unknown  addLocationStrategyAndWait()
 * @method unknown  addScript()
 * @method unknown  addScriptAndWait()
 * @method unknown  addSelection()
 * @method unknown  addSelectionAndWait()
 * @method unknown  allowNativeXpath()
 * @method unknown  allowNativeXpathAndWait()
 * @method unknown  altKeyDown()
 * @method unknown  altKeyDownAndWait()
 * @method unknown  altKeyUp()
 * @method unknown  altKeyUpAndWait()
 * @method unknown  answerOnNextPrompt()
 * @method unknown  assignId()
 * @method unknown  assignIdAndWait()
 * @method unknown  attachFile()
 * @method unknown  break()
 * @method unknown  captureEntirePageScreenshot()
 * @method unknown  captureEntirePageScreenshotAndWait()
 * @method unknown  captureEntirePageScreenshotToStringAndWait()
 * @method unknown  captureScreenshotAndWait()
 * @method unknown  captureScreenshotToStringAndWait()
 * @method unknown  check()
 * @method unknown  checkAndWait()
 * @method unknown  chooseCancelOnNextConfirmation()
 * @method unknown  chooseCancelOnNextConfirmationAndWait()
 * @method unknown  chooseOkOnNextConfirmation()
 * @method unknown  chooseOkOnNextConfirmationAndWait()
 * @method unknown  click()
 * @method unknown  clickAndWait()
 * @method unknown  clickAt()
 * @method unknown  clickAtAndWait()
 * @method unknown  close()
 * @method unknown  contextMenu()
 * @method unknown  contextMenuAndWait()
 * @method unknown  contextMenuAt()
 * @method unknown  contextMenuAtAndWait()
 * @method unknown  controlKeyDown()
 * @method unknown  controlKeyDownAndWait()
 * @method unknown  controlKeyUp()
 * @method unknown  controlKeyUpAndWait()
 * @method unknown  createCookie()
 * @method unknown  createCookieAndWait()
 * @method unknown  deleteAllVisibleCookies()
 * @method unknown  deleteAllVisibleCookiesAndWait()
 * @method unknown  deleteCookie()
 * @method unknown  deleteCookieAndWait()
 * @method unknown  deselectPopUp()
 * @method unknown  deselectPopUpAndWait()
 * @method unknown  doubleClick()
 * @method unknown  doubleClickAndWait()
 * @method unknown  doubleClickAt()
 * @method unknown  doubleClickAtAndWait()
 * @method unknown  dragAndDrop()
 * @method unknown  dragAndDropAndWait()
 * @method unknown  dragAndDropToObject()
 * @method unknown  dragAndDropToObjectAndWait()
 * @method unknown  dragDrop()
 * @method unknown  dragDropAndWait()
 * @method unknown  echo()
 * @method unknown  fireEvent()
 * @method unknown  fireEventAndWait()
 * @method unknown  focus()
 * @method unknown  focusAndWait()
 * @method string   getAlert()
 * @method array    getAllButtons()
 * @method array    getAllFields()
 * @method array    getAllLinks()
 * @method array    getAllWindowIds()
 * @method array    getAllWindowNames()
 * @method array    getAllWindowTitles()
 * @method string   getAttribute()
 * @method array    getAttributeFromAllWindows()
 * @method string   getBodyText()
 * @method string   getConfirmation()
 * @method string   getCookie()
 * @method string   getCookieByName()
 * @method integer  getCursorPosition()
 * @method integer  getElementHeight()
 * @method integer  getElementIndex()
 * @method integer  getElementPositionLeft()
 * @method integer  getElementPositionTop()
 * @method integer  getElementWidth()
 * @method string   getEval()
 * @method string   getExpression()
 * @method string   getHtmlSource()
 * @method string   getLocation()
 * @method string   getLogMessages()
 * @method integer  getMouseSpeed()
 * @method string   getPrompt()
 * @method array    getSelectOptions()
 * @method string   getSelectedId()
 * @method array    getSelectedIds()
 * @method string   getSelectedIndex()
 * @method array    getSelectedIndexes()
 * @method string   getSelectedLabel()
 * @method array    getSelectedLabels()
 * @method string   getSelectedValue()
 * @method array    getSelectedValues()
 * @method unknown  getSpeed()
 * @method unknown  getSpeedAndWait()
 * @method string   getTable()
 * @method string   getText()
 * @method string   getTitle()
 * @method string   getValue()
 * @method boolean  getWhetherThisFrameMatchFrameExpression()
 * @method boolean  getWhetherThisWindowMatchWindowExpression()
 * @method integer  getXpathCount()
 * @method unknown  goBack()
 * @method unknown  goBackAndWait()
 * @method unknown  highlight()
 * @method unknown  highlightAndWait()
 * @method unknown  ignoreAttributesWithoutValue()
 * @method unknown  ignoreAttributesWithoutValueAndWait()
 * @method boolean  isAlertPresent()
 * @method boolean  isChecked()
 * @method boolean  isConfirmationPresent()
 * @method boolean  isCookiePresent()
 * @method boolean  isEditable()
 * @method boolean  isElementPresent()
 * @method boolean  isOrdered()
 * @method boolean  isPromptPresent()
 * @method boolean  isSomethingSelected()
 * @method boolean  isTextPresent()
 * @method boolean  isVisible()
 * @method unknown  keyDown()
 * @method unknown  keyDownAndWait()
 * @method unknown  keyDownNative()
 * @method unknown  keyDownNativeAndWait()
 * @method unknown  keyPress()
 * @method unknown  keyPressAndWait()
 * @method unknown  keyPressNative()
 * @method unknown  keyPressNativeAndWait()
 * @method unknown  keyUp()
 * @method unknown  keyUpAndWait()
 * @method unknown  keyUpNative()
 * @method unknown  keyUpNativeAndWait()
 * @method unknown  metaKeyDown()
 * @method unknown  metaKeyDownAndWait()
 * @method unknown  metaKeyUp()
 * @method unknown  metaKeyUpAndWait()
 * @method unknown  mouseDown()
 * @method unknown  mouseDownAndWait()
 * @method unknown  mouseDownAt()
 * @method unknown  mouseDownAtAndWait()
 * @method unknown  mouseMove()
 * @method unknown  mouseMoveAndWait()
 * @method unknown  mouseMoveAt()
 * @method unknown  mouseMoveAtAndWait()
 * @method unknown  mouseOut()
 * @method unknown  mouseOutAndWait()
 * @method unknown  mouseOver()
 * @method unknown  mouseOverAndWait()
 * @method unknown  mouseUp()
 * @method unknown  mouseUpAndWait()
 * @method unknown  mouseUpAt()
 * @method unknown  mouseUpAtAndWait()
 * @method unknown  mouseUpRight()
 * @method unknown  mouseUpRightAndWait()
 * @method unknown  mouseUpRightAt()
 * @method unknown  mouseUpRightAtAndWait()
 * @method unknown  open()
 * @method unknown  openWindow()
 * @method unknown  openWindowAndWait()
 * @method unknown  pause()
 * @method unknown  refresh()
 * @method unknown  refreshAndWait()
 * @method unknown  removeAllSelections()
 * @method unknown  removeAllSelectionsAndWait()
 * @method unknown  removeScript()
 * @method unknown  removeScriptAndWait()
 * @method unknown  removeSelection()
 * @method unknown  removeSelectionAndWait()
 * @method unknown  retrieveLastRemoteControlLogs()
 * @method unknown  rollup()
 * @method unknown  rollupAndWait()
 * @method unknown  runScript()
 * @method unknown  runScriptAndWait()
 * @method unknown  select()
 * @method unknown  selectAndWait()
 * @method unknown  selectFrame()
 * @method unknown  selectPopUp()
 * @method unknown  selectPopUpAndWait()
 * @method unknown  selectWindow()
 * @method unknown  setBrowserLogLevel()
 * @method unknown  setBrowserLogLevelAndWait()
 * @method unknown  setContext()
 * @method unknown  setCursorPosition()
 * @method unknown  setCursorPositionAndWait()
 * @method unknown  setMouseSpeed()
 * @method unknown  setMouseSpeedAndWait()
 * @method unknown  setSpeed()
 * @method unknown  setSpeedAndWait()
 * @method unknown  shiftKeyDown()
 * @method unknown  shiftKeyDownAndWait()
 * @method unknown  shiftKeyUp()
 * @method unknown  shiftKeyUpAndWait()
 * @method unknown  shutDownSeleniumServer()
 * @method unknown  store()
 * @method unknown  submit()
 * @method unknown  submitAndWait()
 * @method unknown  type()
 * @method unknown  typeAndWait()
 * @method unknown  typeKeys()
 * @method unknown  typeKeysAndWait()
 * @method unknown  uncheck()
 * @method unknown  uncheckAndWait()
 * @method unknown  useXpathLibrary()
 * @method unknown  useXpathLibraryAndWait()
 * @method unknown  waitForCondition()
 * @method unknown  waitForPageToLoad()
 * @method unknown  waitForPopUp()
 * @method unknown  windowFocus()
 * @method unknown  windowMaximize()
 */
abstract class SeleniumTestCase extends \PHPUnit_Extensions_SeleniumTestCase
{
    /**
     * BrowserUrl
     * 
     * @var string
     */
    private $browserUrl;

    /**
     * Get the browser url
     *
     * @return string
     */
    public function getBrowserUrl()
    {
        return $this->browserUrl;
    }

    /**
     * Set the browser url
     *
     * @param string $url
     * @return SeleniumTestCase
     */
    public function setBrowserUrl($url)
    {
        $this->browserUrl = $url;
        parent::setBrowserUrl($url);
        return $this;
    }

    /**
     * Runs the test case and collects the results in a TestResult object.
     * If no TestResult object is passed a new one will be created.
     *
     * @param  \PHPUnit_Framework_TestResult $result
     * @return \PHPUnit_Framework_TestResult
     * @throws \InvalidArgumentException
     */
    public function run(\PHPUnit_Framework_TestResult $result = null)
    {
        if ($result === null) {
            $result = $this->createResult();
        }


        // Code used to prevent setting multiple listeners
        $r = new \ReflectionObject($result);
        /* @var $prop \ReflectionProperty */
        $prop = $r->getProperty('listeners');
        $prop->setAccessible(true);

        $existingListener = false;
        foreach ((array) $prop->getValue($result) as $listener) {
            if ($listener instanceof SeleniumListener) {
                $existingListener = ($listener instanceof SeleniumListener);
                break;
            }
        }

        // Add SeleniumListener if not existing
        if (!$existingListener) {
            $r         = new \ReflectionClass('PHPUnit_Util_Configuration');

            /* @var $instancesProp \ReflectionProperty */
            $instancesProp = $r->getProperty('instances');
            $instancesProp->setAccessible(true);

            $instances = $instancesProp->getValue($instancesProp);
            foreach ($instances as $filename => $config) {
                /* @var $config \PHPUnit_Util_Configuration */
                $listeners = $config->getListenerConfiguration();
                foreach ($listeners as $listener) {
                    if ($listener['class'] == 'Core\Engine\PHPUnit\Selenium\SeleniumListener')  {
                        $browserUrl = $listener['arguments'][0];

                        $listener = new SeleniumListener($browserUrl);
                        $result->addListener($listener);
                        break 2;
                    }
                }
            }
        }

        return parent::run($result);
    }
}