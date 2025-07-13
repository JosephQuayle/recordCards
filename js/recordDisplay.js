// A $( document ).ready() block.
$(document).ready(function () {
  console.log("ready!");

  //get all client info and save to client details
  $("#saveDetails").click(function (e) {
    console.log("details saved");

    e.preventDefault();

    // Gather form values
    const data = {
      clientFirstName: $("#fname").val(),
      clientSurname: $("#sname").val(),
      clientEmail: $("#email").val(),
      clientDOB: $("#dob").val(),
      clientMobile: $("#phone").val(),
      clientAddress: $("#address").val(),
      clientGPAddress: $("#gpaddress").val(),
      clientOccupation: $("#occupation").val(),
      clientMedical: $("#medical").val(),
      clientSignature: $("#sign").val(),
      clientSignedDate: $("#date").val(),
    };

    // Send to server
    $.ajax({
      url: "recordPost.php",
      type: "POST",
      data: data,
      dataType: "json",
    })
      .done(function (res) {
        if (res.success) {
          alert("Client saved! Record ID: " + res.recordId);
          //clear form here
          $("#recordForm").find("input").val("");
          $("#recordForm").find("input, textarea").val("");
          $("#medicalList").empty();
          // clears the visible condition list
          $("#medicalInput").val(""); // clears the input box
          $("#medical").val(""); // clears the hidden field sent to the DB
        } else {
          alert("Error: " + res.error);
        }
      })
      .fail(function (xhr, status, err) {
        console.error("AJAX error:", status, err);
        alert("Server error, check console.");
      });
  });

  // 1) view clients page: generate & copy signed link
  $("button.genLink").on("click", function () {
    const btn = $(this);
    const clientId = btn.data("clientid");

    btn.prop("disabled", true).text("Generating…");

    $.getJSON("generateLink.php", { client: clientId })
      .done(function (res) {
        if (res.link) {
          navigator.clipboard
            .writeText(res.link)
            .then(() => {
              btn.text("Copied!");
              setTimeout(() => {
                btn.prop("disabled", false).text("Generate Link");
              }, 2000);
            })
            .catch((err) => {
              console.error("Clipboard error", err);
              alert("Copy failed: " + err);
              btn.prop("disabled", false).text("Generate Link");
            });
        } else {
          alert("Error: " + res.error);
          btn.prop("disabled", false).text("Generate Link");
        }
      })
      .fail(function () {
        alert("Server error. Try again later.");
        btn.prop("disabled", false).text("Generate Link");
      });
  });

  // // 3) Student page: submit treatment form
  // // Student page: submit treatment form
  // $("#saveTreatment").on("click", function () {
  //   const $btn = $(this);
  //   $btn.prop("disabled", true).text("Saving…");

  //   $.ajax({
  //     url: "saveTreatment.php",
  //     method: "POST",
  //     dataType: "json",
  //     data: $("#treatmentForm").serialize(),
  //   })
  //     .done(function (res) {
  //       if (res.success) {
  //         alert("Treatment saved (ID: " + res.recordId + ")");
  //         $("#treatmentForm")[0].reset();
  //       } else {
  //         alert("Error: " + res.error);
  //       }
  //     })
  //     .fail(function (xhr, status, err) {
  //       console.error("AJAX failure:", status, err);
  //       console.error("Response text:", xhr.responseText);
  //       alert("Server error — check console for details.");
  //     })
  //     .always(function () {
  //       $btn.prop("disabled", false).text("Save");
  //     });
  // });
  let savingTreatment = false;

  $("#saveTreatment")
    .off("click")
    .on("click", function (e) {
      e.preventDefault();
      if (savingTreatment) return;
      savingTreatment = true;

      const $btn = $(this);
      $btn.prop("disabled", true).text("Saving…");

      syncProducts();

      console.log("Serialized data:", $("#treatmentForm").serialize());

      // capture form values
      const treatment = $("#treatment").val();
      const productsUsed = $("#productsUsed").val();
      const studentDate = $("#studentDate").val();
      const studentFirstName = $("#studentFirstName").val();
      const studentSurname = $("#studentSurname").val();
      const studentSignature = $("#studentSignature").val();

      $.ajax({
        url: "saveTreatment.php",
        method: "POST",
        dataType: "json",
        data: $("#treatmentForm").serialize(),
      })
        .done(function (res) {
          if (res.success) {
            // remove "no previous treatments" row if it exists
            $("#treatmentRows .no-prev-recs").remove();

            // inject new row
            const rowHtml = `
          <tr>
            <td>${escapeHtml(treatment)}</td>
            <td>${nl2br(escapeHtml(productsUsed))}</td>
            <td>${escapeHtml(studentDate)}</td>
            <td>${escapeHtml(studentFirstName)}</td>
            <td>${escapeHtml(studentSurname)}</td>
            <td>${escapeHtml(studentSignature)}</td>
            <td></td>
          </tr>`;
            $("#newEntryRow").after(rowHtml);

            // reset forms
            $("#treatmentForm")[0].reset();
            $("#productList").empty();
          } else {
            alert("Error: " + res.error);
          }
          console.log("Date value submitted:", studentDate);
        })
        .fail(function (xhr, status, err) {
          console.error("Save failed:", status, err, xhr.responseText);
          alert("Server error, check console.");
        })
        .always(function () {
          savingTreatment = false;
          $btn.prop("disabled", false).text("Save");
        });
    });

  // helpers
  function escapeHtml(s) {
    return String(s)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }
  function nl2br(s) {
    return String(s).replace(/\r?\n/g, "<br>");
  }

  $("#addProduct").on("click", function () {
    const product = $("#productInput").val().trim();
    if (product === "") return;

    // Add to visible list
    $("#productList").append(
      `<li>${escapeHtml(product)} <button class="remove">×</button></li>`
    );

    // Clear input
    $("#productInput").val("");

    // Sync hidden field
    syncProducts();
  });

  // Remove items
  $("#productList").on("click", ".remove", function () {
    $(this).parent().remove();
    syncProducts();
  });

  function syncProducts() {
    const items = [];
    $("#productList li").each(function () {
      items.push($(this).text().replace("×", "").trim());
    });
    $("#productsUsed").val(items.join("\n"));
  }

  $("#addMedical").on("click", function () {
    const condition = $("#medicalInput").val().trim();
    if (condition === "") return;

    // Add to visible list
    $("#medicalList").append(
      `<li>${escapeHtml(condition)} <button class="remove">×</button></li>`
    );

    // Clear input
    $("#medicalInput").val("");

    // Sync hidden field
    syncMedical();
  });

  $("#medicalList").on("click", ".remove", function () {
    $(this).parent().remove();
    syncMedical();
  });

  function syncMedical() {
    const conditions = [];
    $("#medicalList li").each(function () {
      conditions.push($(this).text().replace("×", "").trim());
    });
    $("#medical").val(conditions.join("\n")); // sync with hidden <textarea id="medical">
  }

  function escapeHtml(text) {
    return text.replace(/[&<>"']/g, function (char) {
      return {
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        '"': "&quot;",
        "'": "&#039;",
      }[char];
    });
  }
});
