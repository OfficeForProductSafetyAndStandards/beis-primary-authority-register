package uk.gov.beis.pageobjects.TransferPartnerships;

import java.io.IOException;
import java.time.Duration;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import uk.gov.beis.pageobjects.BasePageObject;

public class AuthorityTransferSelectionPage extends BasePageObject {
	
	@FindBy(id = "edit-authority")
	private WebElement authorityTextfield;
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	public AuthorityTransferSelectionPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public PartnershipMigrationSelectionPage searchAuthority(String authorityName) {
		authorityTextfield.clear();
		authorityTextfield.sendKeys(authorityName);
		
		WebElement widget = driver.findElement(By.id("ui-id-1"));
		
		WebDriverWait wait = new WebDriverWait(driver, Duration.ofSeconds(5));
		
		wait.until(ExpectedConditions.visibilityOf(widget));
		
		if(widget.isDisplayed()) {
			widget.click();
		}
		
		continueBtn.click();
		
		return PageFactory.initElements(driver, PartnershipMigrationSelectionPage.class);
	}
}
