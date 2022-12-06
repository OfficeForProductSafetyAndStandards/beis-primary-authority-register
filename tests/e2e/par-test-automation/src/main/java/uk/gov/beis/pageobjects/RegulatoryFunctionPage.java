package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class RegulatoryFunctionPage extends BasePageObject {

	public RegulatoryFunctionPage() throws ClassNotFoundException, IOException {
		super();
	}

	public PartnershipApprovalPage proceed() {
		driver.findElement(By.xpath("//input[contains(@value,'Continue')]")).click();
		return PageFactory.initElements(driver, PartnershipApprovalPage.class);
	}
}
