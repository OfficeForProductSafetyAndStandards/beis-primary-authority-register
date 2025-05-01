package uk.gov.beis.pageobjects;

import java.io.BufferedReader;
import java.io.FileReader;
import java.io.IOException;
import java.util.Map;

import org.apache.commons.lang3.RandomStringUtils;
import org.junit.Assert;

import java.time.Duration;
import java.util.ArrayList;
import io.cucumber.java.en.*;

import org.openqa.selenium.By;
import org.openqa.selenium.JavascriptExecutor;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.remote.LocalFileDetector;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.FluentWait;
import org.openqa.selenium.support.ui.WebDriverWait;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.openqa.selenium.remote.RemoteWebDriver;
import org.openqa.selenium.remote.LocalFileDetector;

import uk.gov.beis.enums.Browser;
import uk.gov.beis.helper.ScenarioContext;
import uk.gov.beis.supportfactory.BrowserFactory;

public class BasePageObject {

	@FindBy(linkText = "Primary Authority Register")
	private WebElement headerTextLink;

	@FindBy(linkText = "Sign out")
	private WebElement signOutLink;

	public static WebDriver driver;
	public String pageName;

	protected Logger LOG = LoggerFactory.getLogger(BasePageObject.class);
	protected static FluentWait<WebDriver> wait;
	private static JavascriptExecutor javascriptExecutor;

	private String errorSummaryLocator = "//div/ul/li[contains(normalize-space(), \"?\")]";
	private String errorMessageLocator = "//div/span[contains(normalize-space(), \"?\")]";

	// create a web driver instance when BasePageObject instantiated using the shared driver
	public BasePageObject() {
		driver = ScenarioContext.lastDriver;

		javascriptExecutor = (JavascriptExecutor) driver;
	}

	public void clickHeaderLink() {
        waitForElementToBeVisible(By.linkText("Primary Authority Register"), 2000);
		headerTextLink.click();
	}

	public void clickSignOut() {
        waitForElementToBeClickable(By.linkText("Sign out"), 3000);
		signOutLink.click();
        waitForPageLoad();
	}

	public Boolean checkErrorSummary(String errorMessage) {
        //waitForElementToBeVisible(By.xpath(errorSummaryLocator.replace("?", errorMessage)), 2000);
		return driver.findElement(By.xpath(errorSummaryLocator.replace("?", errorMessage))).isDisplayed();
	}

	public Boolean checkErrorMessage(String errorMessage) {
		return driver.findElement(By.xpath(errorMessageLocator.replace("?", errorMessage))).isDisplayed();
	}

	public void uploadDocument(WebElement filebrowser, String filename) {
		if(BrowserFactory.browser == Browser.Chrome) {
			filebrowser.sendKeys(System.getProperty("user.dir") + "/" + filename);
           // filebrowser.sendKeys("/app/" + filename);
		}
		else if (BrowserFactory.browser == Browser.Firefox) {
			filebrowser.sendKeys(System.getProperty("user.dir") + "\\" + filename);
		}
        else if (BrowserFactory.browser == Browser.ChromeDocker) {

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

	public static String executeJs(String script) {
		return String.valueOf(javascriptExecutor.executeScript(script));
	}

	public void executeJavaScript(String script, WebElement element) {
		javascriptExecutor.executeScript(script, element);
	}

    // Wait until element is visible
    public void waitForElementToBeVisible(By locator, int timeoutInSeconds) {
        WebDriverWait wait = new WebDriverWait(driver, Duration.ofMillis(timeoutInSeconds));
        wait.until(ExpectedConditions.visibilityOfElementLocated(locator));
    }

    // Wait until element is visible
    public void waitForElementToBeClickable(By locator, int timeoutInSeconds) {
        WebDriverWait wait = new WebDriverWait(driver, Duration.ofMillis(timeoutInSeconds));
        wait.until(ExpectedConditions.elementToBeClickable(locator));
    }

    public void waitForPageLoad() {
        new WebDriverWait(driver, Duration.ofSeconds(60))
            .until(webDriver -> ((org.openqa.selenium.JavascriptExecutor) webDriver)
                .executeScript("return document.readyState").equals("complete"));
        try {
            Thread.sleep(1000);
        } catch (InterruptedException e) {
            throw new RuntimeException(e);
        }
    }

	public void refresh() {
		driver.navigate().refresh();
	}
}
