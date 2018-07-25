GolfExample = {};

GolfExample.CourseActivity = {
    id: "http://formagri.com/co/Revision_epreuve_5_web.html",
    definition: {
        type: "http://adlnet.gov/expapi/activities/course",
        name: {
            "fr-FR": "co/Revision_epreuve_5_web.html - Tin Can Course"
        },
        description: {
            "fr-FR": "Revisez-vos-connaissances-de-zootechnie-generale-et-ses-applications-pour-l-epreuve-5"
        }
    }
};

GolfExample.getContext = function(parentActivityId) {
    var ctx = {
        contextActivities: {
            grouping: {
                id: GolfExample.CourseActivity.id
            }
        }
    };
    if (parentActivityId !== undefined && parentActivityId !== null) {
        ctx.contextActivities.parent = {
            id: parentActivityId
        };
    }
    return ctx;
};
