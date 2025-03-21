package uk.gov.beis.pageobjects.UserManagement;

import java.io.IOException;
import java.time.Duration;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import uk.gov.beis.pageobjects.BasePageObject;

public class UserMembershipPage extends BasePageObject {
	@FindBy(id = "edit-par-data-organisation-id")
	private WebElement organisationTextField;
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	private String authorityRadioLocator = "//label[contains(normalize-space(), '?')]/preceding-sibling::input";
	
	public UserMembershipPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void chooseAuthorityMembership(String authorityName) {
		
		if(!driver.findElements(By.xpath(authorityRadioLocator.replace("?", authorityName))).isEmpty()) {
			WebElement authorityRadio = driver.findElement(By.xpath(authorityRadioLocator.replace("?", authorityName)));
			authorityRadio.click();
		}
		else {
			WebElement authorityTextField = driver.findElement(By.id("edit-par-data-authority-id"));
			authorityTextField.clear();
			authorityTextField.sendKeys(authorityName);
			
			WebElement widget = driver.findElement(By.id("ui-id-1"));
			
			WebDriverWait wait = new WebDriverWait(driver, Duration.ofSeconds(5));
			
			wait.until(ExpectedConditions.visibilityOf(widget));
			
			if(widget.isDisplayed()) {
				widget.click();
			}
		}
	}
	
	public void chooseOrganisationMembership(String organisationName) {
		if(organisationTextField != null) {
			organisationTextField.clear();
			organisationTextField.sendKeys(organisationName);
			
			WebElement widget = driver.findElement(By.id("ui-id-2"));
			
			WebDriverWait wait = new WebDriverWait(driver, Duration.ofSeconds(5));
			
			wait.until(ExpectedConditions.visibilityOf(widget));
			
			if(widget.isDisplayed()) {
				widget.click();
			}
		}
	}
	
	public void clickContinueButton() {
		continueBtn.click();
	}
}
