package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.NoSuchElementException;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class PARDashboardPage extends BasePageObject {

	public PARDashboardPage() throws ClassNotFoundException, IOException {
		super();
//		checkAndAcceptCookies();
	}
	
	@FindBy(xpath = "//div[@id='block-par-theme-page-title']")
	private WebElement dashBoard;
	
	@FindBy(linkText = "Apply for a new partnership")
	WebElement applyPartnershipBtn;
	
	@FindBy(linkText = "See your partnerships")
	WebElement viewPartnershipBtn;
	
	public PARAuthorityPage selectApplyForNewPartnership() {
		applyPartnershipBtn.click();
		return PageFactory.initElements(driver, PARAuthorityPage.class);
	}
	
	public PartnershipSearchPage selectSeePartnerships() {
		viewPartnershipBtn.click();
		return PageFactory.initElements(driver, PartnershipSearchPage.class);
	}
	
	public String checkPage() {
		return dashBoard.getText();
	}
	
	
	public PARDashboardPage checkAndAcceptCookies() {
		try {
			driver.findElement(By.xpath("//button[contains(text(),'Accept')]")).click();
		} catch (NoSuchElementException e) {
			// do nothing
		}
		return PageFactory.initElements(driver, PARDashboardPage.class);
	}
	
}
