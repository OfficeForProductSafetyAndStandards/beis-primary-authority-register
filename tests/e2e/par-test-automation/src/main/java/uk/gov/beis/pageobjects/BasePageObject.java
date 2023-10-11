package uk.gov.beis.pageobjects;

import java.io.BufferedReader;
import java.io.FileReader;

import java.util.ArrayList;
import java.util.InputMismatchException;

import org.openqa.selenium.By;
import org.openqa.selenium.JavascriptExecutor;
import org.openqa.selenium.NoSuchElementException;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebDriverException;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.FluentWait;
import org.openqa.selenium.support.ui.Select;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

import uk.gov.beis.enums.Browser;
import uk.gov.beis.helper.ScenarioContext;
import uk.gov.beis.supportfactory.BrowserFactory;

public class BasePageObject {

	public static WebDriver driver;
	public String pageName;

	private static final long DRIVER_WAIT_TIME = 15;
	protected Logger LOG = LoggerFactory.getLogger(BasePageObject.class);
	protected static FluentWait<WebDriver> wait;
	private static JavascriptExecutor js;
	private static By currentIframe;

	// create a web driver instance when BasePageObject instantiated using the shared driver
	public BasePageObject() {
		driver = ScenarioContext.lastDriver;
		//wait = new WebDriverWait(driver, DRIVER_WAIT_TIME).pollingEvery(1, TimeUnit.SECONDS).ignoring(StaleElementReferenceException.class);
		js = (JavascriptExecutor) driver;
	}
	
	public void uploadDocument(WebElement filebrowser, String filename) {
		if(BrowserFactory.browser == Browser.Chrome) {
			filebrowser.sendKeys(System.getProperty("user.dir") + "/" + filename);
		}
		else if (BrowserFactory.browser == Browser.Firefox) {
			filebrowser.sendKeys(System.getProperty("user.dir") + "\\" + filename);
		}
	}
	
	public String[] getColumnFromCSV(String filename, int column, String delimiter) {
		String path = "";
		
		if(BrowserFactory.browser == Browser.Chrome) {
			path = System.getProperty("user.dir") + "/" + filename;
		}
		else if (BrowserFactory.browser == Browser.Firefox) {
			path = System.getProperty("user.dir") + "\\" + filename;
		}
		
		String data[];
		String currentLine;
		ArrayList<String> columnData = new ArrayList<String>();
		
		try {
			FileReader fr = new FileReader(path);
			BufferedReader br = new BufferedReader(fr);
			
			while((currentLine = br.readLine()) != null) {
				data = currentLine.split(delimiter);
				columnData.add(data[column]);
			}
			
			br.close();
		}
		catch(Exception e) {
			System.out.println(e);
			return null;
		}
		
		return columnData.toArray(new String[0]);
	}

	public WebElement waitUntilCickable(WebElement element) {
		return wait.until(ExpectedConditions.elementToBeClickable(element));
	}

	// added functionality to wait for specific element in DOM
	public WebElement waitForExpectedElement(WebElement element) {
		return wait.until(ExpectedConditions.visibilityOf(element));
	}
	
	// same as above but returns a boolean (true or false) depending on if the element is displayed
	public boolean isElementDisplayed(WebElement element) {
		return waitForExpectedElement(element).isDisplayed();
	}

	public void click(WebElement element) {
		try {
			scrollToElement(element);
			waitUntilCickable(element).click();
		} catch (WebDriverException stale) {
			scrollToElement(element);
			waitUntilCickable(element).click();
		}
	}

	public String getText(WebElement element) {
		return waitForExpectedElement(element).getText();
	}

	public void selectDropDownValue(WebElement element, String value) {
		waitForExpectedElement(element);
		Select select = new Select(element);
		try {
			select.selectByVisibleText(value);
		} catch (NoSuchElementException no) {
			throw new InputMismatchException("There is not value in the the dropdown list matching: " + value);
		}
	}
	
	public WebElement scrollToElement(WebElement element) {
		try {
			executeJs("function scrollIntoView(el) {" + "var offsetTop = $(el).offset().top;"
					+ "var adjustment = Math.max(0,( $(window).height() - $(el).outerHeight(true) ) / 2);"
					+ "var scrollTop = offsetTop - adjustment;" + "$('html,body').animate({" + "scrollTop: scrollTop"
					+ "}, 0);" + "} scrollIntoView(arguments[0]);", element);
		} catch (WebDriverException web) {
			scrollIntoView(element);
		}
		return element;
	}

	public WebElement scrollIntoView(WebElement element) {
		try {
			executeJs("arguments[0].scrollIntoView(true);", element);
		} catch (WebDriverException web) {
			// do nothing
		}
		return element;
	}

	public static String executeJs(String script) {
		return String.valueOf(js.executeScript(script));
	}

	public String executeJs(String script, WebElement element) {
		return String.valueOf(js.executeScript(script, element));
	}

	public void refresh() {
		driver.navigate().refresh();
	}
}
