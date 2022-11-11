package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class PARDashboardPage extends BasePageObject {

	public PARDashboardPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	@FindBy(xpath = "//div[@id='block-par-theme-page-title']")
	private WebElement dashBoard;
	
	@FindBy(linkText = "Apply for a new partnership")
	WebElement applyPartnershipBtn;
	
    private String locator = "//label[contains(text(),'?')]";
	
	public PARAuthorityPage selectApplyForNewPartnership() {
		applyPartnershipBtn.click();
		return PageFactory.initElements(driver, PARAuthorityPage.class);
	}
	
	public String checkPage() {
		return dashBoard.getText();
	}
	
}
